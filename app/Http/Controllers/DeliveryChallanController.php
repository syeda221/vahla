<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\DeliveryChallan;
use App\Models\DeliveryChallanItem;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeliveryChallanController extends Controller
{
    public function index($saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $challans = DeliveryChallan::with('items.product')->where('sale_id', $saleId)->get();

        return view('admin_panel.sale.delivery_challan.list', compact('sale', 'challans'));
    }

    public function create($saleId)
    {
        $sale = Sale::with('items.product')->findOrFail($saleId);

        if ($sale->sale_type !== 'sales_order') {
            return redirect()->back()->with('error', 'Delivery Challans can only be created for Sales Orders.');
        }

        if ($sale->delivery_status === 'delivered') {
            return redirect()->back()->with('error', 'This order is already fully delivered.');
        }

        return view('admin_panel.sale.delivery_challan.create', compact('sale'));
    }

    public function store(Request $request, $saleId)
    {
        $sale = Sale::with('items.product')->findOrFail($saleId);

        if ($sale->sale_type !== 'sales_order') {
            return redirect()->back()->with('error', 'Invalid sale type.');
        }

        $quantities = $request->input('dc_qty', []);
        
        // Filter out items with 0 or empty qty
        $deliveries = array_filter($quantities, function($qty) {
            return is_numeric($qty) && $qty > 0;
        });

        if (empty($deliveries)) {
            return redirect()->back()->with('error', 'Please provide valid quantities to deliver.');
        }

        DB::beginTransaction();
        try {
            // Validate all quantities first
            foreach ($deliveries as $itemId => $qty) {
                $item = $sale->items->where('id', $itemId)->first();
                if (!$item) {
                    throw new \Exception('Invalid sale item selected.');
                }
                
                $variant = [];
                if (!empty($item->color)) {
                    $b64 = base64_decode($item->color, true);
                    if ($b64 !== false) $variant = json_decode($b64, true) ?: [];
                    if (empty($variant)) $variant = json_decode($item->color, true) ?: [];
                }
                $sizeMode = $item->size_mode ?? optional($item->product)->size_mode ?? 'by_size';
                $vUnit = strtolower($variant['unit'] ?? optional(optional($item->product)->unit)->name ?? '');
                
                $dispQtyFactor = 1;
                if (in_array($sizeMode, ['by_kg', 'by_gm'])) {
                    if (in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                        $wtConv = (float)($variant['conv_factor'] ?? $item->pieces_per_box ?? 1);
                        if ($wtConv <= 0) $wtConv = 1;
                        $dispQtyFactor = 1 / $wtConv;
                    } elseif (in_array($vUnit, ['gm', 'g'])) {
                        $dispQtyFactor = 1000;
                    }
                }
                
                $deliveryQtyBase = $qty / $dispQtyFactor;
                
                $remaining = $item->remaining_qty;
                // Add a tiny epsilon for float comparison
                if ($deliveryQtyBase > $remaining + 0.0001) {
                    throw new \Exception("Cannot deliver {$qty} {$vUnit} of {$item->product_name}. Only " . ($remaining * $dispQtyFactor) . " remaining.");
                }
            }

            // Create DC
            $dcCount = DeliveryChallan::where('sale_id', $saleId)->count() + 1;
            $baseNo = $sale->invoice_no ?: (($sale->sale_type === 'sales_order' ? 'SO-' : ($sale->sale_type === 'quotation' ? 'QUO-' : '#')) . str_pad($sale->id, 4, '0', STR_PAD_LEFT));

            $isAlreadyInvoiced = ($sale->sale_type === 'direct_sale' || $sale->sale_status === 'posted') ? 1 : 0;
            $dc = DeliveryChallan::create([
                'sale_id' => $saleId,
                'customer_id' => $sale->customer_id,
                'dc_number' => $baseNo . '-DC' . str_pad($dcCount, 2, '0', STR_PAD_LEFT),
                'dc_date' => now()->format('Y-m-d'),
                'status' => 'confirmed', // We confirm it immediately as per plan
                'is_invoiced' => $isAlreadyInvoiced,
                'invoice_id' => $isAlreadyInvoiced ? $sale->id : null,
                'remarks' => $request->input('remarks'),
                'created_by' => auth()->id()
            ]);

            $allDelivered = true;
            $anyDelivered = false;

            foreach ($sale->items as $item) {
                if (isset($deliveries[$item->id])) {
                    $deliveryQtyInput = (float) $deliveries[$item->id];
                    
                    // --- Conversion Logic for Displayed Unit ---
                    $variant = [];
                    if (!empty($item->color)) {
                        $b64 = base64_decode($item->color, true);
                        if ($b64 !== false) $variant = json_decode($b64, true) ?: [];
                        if (empty($variant)) $variant = json_decode($item->color, true) ?: [];
                    }
                    $sizeMode = $item->size_mode ?? optional($item->product)->size_mode ?? 'by_size';
                    $vUnit = strtolower($variant['unit'] ?? optional(optional($item->product)->unit)->name ?? '');
                    
                    // INFER UNIT FROM PRICE RATIO
                    if (empty($variant['unit']) && !in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces']) && in_array($sizeMode, ['by_kg', 'by_gm'])) {
                        $grossTotal = (float)$item->total + (float)$item->discount_amount;
                        $basePricePerKg = ($item->total_pieces > 0) ? ($grossTotal / $item->total_pieces) : 0;
                        $storedPrice = (float) $item->price;
                        $wtConvForInference = (float)($variant['conv_factor'] ?? $item->pieces_per_box ?? 1);
                        if ($wtConvForInference <= 0) $wtConvForInference = 1;
                        
                        if ($storedPrice > 0 && $basePricePerKg > 0) {
                            $ratio = round($storedPrice / $basePricePerKg, 4);
                            if (abs($ratio - $wtConvForInference) < 0.001) {
                                $vUnit = 'pcs';
                            } elseif (abs($ratio - 0.001) < 0.0001) {
                                $vUnit = 'gm';
                            } elseif (abs($ratio - 1) < 0.001) {
                                $vUnit = 'kg';
                            }
                        }
                    }

                    // INFER UNIT FROM PRICE RATIO
                    if (empty($variant['unit']) && !in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces']) && in_array($sizeMode, ['by_kg', 'by_gm'])) {
                        $grossTotal = (float)$item->total + (float)$item->discount_amount;
                        $basePricePerKg = ($item->total_pieces > 0) ? ($grossTotal / $item->total_pieces) : 0;
                        $storedPrice = (float) $item->price;
                        $wtConvForInference = (float)($variant['conv_factor'] ?? $item->pieces_per_box ?? 1);
                        if ($wtConvForInference <= 0) $wtConvForInference = 1;
                        
                        if ($storedPrice > 0 && $basePricePerKg > 0) {
                            $ratio = round($storedPrice / $basePricePerKg, 4);
                            if (abs($ratio - $wtConvForInference) < 0.001) {
                                $vUnit = 'pcs';
                            } elseif (abs($ratio - 0.001) < 0.0001) {
                                $vUnit = 'gm';
                            } elseif (abs($ratio - 1) < 0.001) {
                                $vUnit = 'kg';
                            }
                        }
                    }

                    $dispQtyFactor = 1;
                    if (in_array($sizeMode, ['by_kg', 'by_gm'])) {
                        if (in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                            $wtConv = (float)($variant['conv_factor'] ?? $item->pieces_per_box ?? 1);
                            if ($wtConv <= 0) $wtConv = 1;
                            $dispQtyFactor = 1 / $wtConv;
                        } elseif (in_array($vUnit, ['gm', 'g'])) {
                            $dispQtyFactor = 1000;
                        }
                    }
                    
                    $deliveryQty = $deliveryQtyInput / $dispQtyFactor;
                    
                    // Deduct from stock immediately upon confirmed DC
                    $warehouseId = $item->warehouse_id ?? 1;
                    $productId = $item->product_id;

                    if ($productId) { // Skip manual items for stock tracking if not supported
                        $product = $item->product;
                        $ppb = $product && $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                        
                        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                            ->where('product_id', $productId)
                            ->lockForUpdate()
                            ->first();

                        if ($stock) {
                            $stock->total_pieces -= $deliveryQty;
                            $stock->quantity = $stock->total_pieces / $ppb;
                            $stock->save();
                        } else {
                            $stock = WarehouseStock::create([
                                'warehouse_id' => $warehouseId,
                                'product_id' => $productId,
                                'total_pieces' => -$deliveryQty,
                                'quantity' => -$deliveryQty / $ppb,
                            ]);
                        }

                        // Add Movement
                        StockMovement::create([
                            'product_id' => $productId,
                            'type' => 'out',
                            'qty' => -$deliveryQty,
                            'ref_type' => 'DELIVERY_CHALLAN',
                            'ref_id' => $dc->id,
                            'note' => "DC #{$dc->dc_number} for Sale #{$baseNo} (Warehouse #{$warehouseId})",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Create DC Item
                    DeliveryChallanItem::create([
                        'delivery_challan_id' => $dc->id,
                        'sale_item_id' => $item->id,
                        'product_id' => $productId,
                        'color' => $item->color,
                        'warehouse_id' => $warehouseId,
                        'delivered_qty' => $deliveryQty,
                        'boxes' => $deliveryQtyInput,
                        'loose_pieces' => 0,
                        'price' => $item->price,
                    ]);

                    // Update Sale Item
                    $item->delivered_qty += $deliveryQty;
                    $item->save();
                    $anyDelivered = true;
                }
            }

            $sale->recalculateDeliveryStatus();

            DB::commit();

            $targetRoute = 'sale.index';
            if ($sale->sale_type === 'quotation') {
                $targetRoute = 'quotations.index';
            } elseif ($sale->sale_type === 'sales_order') {
                $targetRoute = 'sales_orders.index';
            }
            return redirect()->route($targetRoute)->with('success', 'Delivery Challan created successfully and stock deducted.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Delivery Challan Error: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function print($id)
    {
        $dc = DeliveryChallan::with(['sale.customer_relation', 'items.product'])->findOrFail($id);
        return view('admin_panel.sale.delivery_challan.print', compact('dc'));
    }

    public function generateInvoiceForDc($id)
    {
        $dc = DeliveryChallan::with(['items.product', 'sale.customer_relation', 'customer'])->findOrFail($id);

        if ($dc->is_invoiced) {
            return redirect()->back()->with('error', 'This Delivery Challan has already been invoiced.');
        }

        if ($dc->items->isEmpty()) {
            return redirect()->back()->with('error', 'This Delivery Challan has no items to invoice.');
        }

        DB::beginTransaction();
        try {
            $customerId = $dc->customer_id ?: optional($dc->sale)->customer_id;
            if (!$customerId) {
                throw new \Exception('No customer associated with this Delivery Challan.');
            }

            $sale = new Sale();
            $sale->customer_id = $customerId;
            $sale->sale_type = 'sales_order';
            $sale->sale_status = 'posted';
            $sale->delivery_status = 'delivered';
            
            // Allow backdating invoice if sale_date is provided
            if (request()->filled('sale_date')) {
                $sale->created_at = \Carbon\Carbon::parse(request('sale_date'))->format('Y-m-d H:i:s');
            }
            $invoiceDate = $sale->created_at ? $sale->created_at->format('Y-m-d') : now()->format('Y-m-d');

            $chosenPrefix = request('prefix');
            $activePrefix = in_array(strtoupper($chosenPrefix), ['TAX', 'CO', 'INV']) ? strtoupper($chosenPrefix) : 'INV';
            $invInput = request('invoice_no') ?: request('Invoice_no');
            
            if ($invInput) {
                $generatedNo = \App\Models\InvoiceSeries::normalizeNumber($invInput, $activePrefix);
            } else {
                $generatedNo = \App\Models\InvoiceSeries::generateNextNo($activePrefix);
            }
            $sale->invoice_no = $generatedNo;
            $sale->parent_quotation_id = $dc->sale_id;
            $sale->reference = 'Invoice for DC: ' . $dc->dc_number;

            $totalBillAmount = 0;
            $totalItems = 0;
            $saleItemsData = [];

            foreach ($dc->items as $dcItem) {
                $qty = (float) $dcItem->delivered_qty;
                $price = (float) ($dcItem->price > 0 ? $dcItem->price : optional($dcItem->saleItem)->price ?? 0);
                $lineTotal = $qty * $price;

                $saleItemsData[] = [
                    'product_id' => $dcItem->product_id,
                    'warehouse_id' => $dcItem->warehouse_id ?? 1,
                    'color' => $dcItem->color,
                    'product_name' => optional($dcItem->product)->item_name ?? 'Product #' . $dcItem->product_id,
                    'qty' => $qty,
                    'total_pieces' => $qty,
                    'price' => $price,
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'total' => $lineTotal,
                    'delivered_qty' => $qty,
                ];

                $totalBillAmount += $lineTotal;
                $totalItems += $qty;
            }

            $sale->total_bill_amount = $totalBillAmount;
            $sale->total_net = $totalBillAmount;
            $sale->total_items = $totalItems;
            $sale->cash = 0;
            $sale->change = 0;
            $sale->save();

            \App\Models\InvoiceSeries::incrementCounterForInvoice($generatedNo);

            foreach ($saleItemsData as $siData) {
                $saleItem = new \App\Models\SaleItem($siData);
                $saleItem->sale_id = $sale->id;
                $saleItem->save();
            }

            $dc->is_invoiced = 1;
            $dc->save();

            $balanceService = app(\App\Services\BalanceService::class);
            $custForVoucher = \App\Models\Customer::find($customerId);
            if ($custForVoucher) {
                $balanceService->createSaleVoucher(
                    $custForVoucher,
                    $sale->total_net,
                    $sale->invoice_no,
                    $invoiceDate
                );

                $ledger = \App\Models\CustomerLedger::where('customer_id', $custForVoucher->id)->latest('id')->first();
                $prev_bal = $ledger ? $ledger->closing_balance : ($custForVoucher->previous_balance ?? 0);
                $new_bal = $prev_bal + $sale->total_net;

                \App\Models\CustomerLedger::create([
                    'customer_id' => $custForVoucher->id,
                    'admin_or_user_id' => auth()->id() ?? 1,
                    'description' => 'Sale Invoice #'.$sale->invoice_no . ' (DC #' . $dc->dc_number . ')',
                    'previous_balance' => $prev_bal,
                    'closing_balance' => $new_bal,
                    'opening_balance' => 0,
                ]);

                $custForVoucher->previous_balance = $new_bal;
                $custForVoucher->save();
            }

            if ($dc->sale_id) {
                $parentSale = Sale::find($dc->sale_id);
                if ($parentSale) {
                    $parentSale->recalculateDeliveryStatus();
                    $uninvoicedDcsCount = DeliveryChallan::where('sale_id', $parentSale->id)->where('is_invoiced', 0)->count();
                    if ($parentSale->delivery_status === 'delivered' && $uninvoicedDcsCount === 0) {
                        $parentSale->sale_status = 'posted';
                        $parentSale->save();
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', "Invoice {$sale->invoice_no} generated successfully for Delivery Challan {$dc->dc_number}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error generating invoice: ' . $e->getMessage());
        }
    }
}
