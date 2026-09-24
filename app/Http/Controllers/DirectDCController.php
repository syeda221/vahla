<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\DeliveryChallan;
use App\Models\DeliveryChallanItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectDCController extends Controller
{
    public function index()
    {
        $challans = DeliveryChallan::with(['customer', 'items.product', 'sale.customer_relation'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admin_panel.direct_dc.index', compact('challans'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $products = Product::orderBy('item_name')->get();
        
        $lastDC = DeliveryChallan::where('dc_number', 'like', 'DDC-%')->latest('id')->first();
        $dcCount = $lastDC ? (int) preg_replace('/[^0-9]/', '', $lastDC->dc_number) : 0;
        $nextDcNumber = 'DDC-' . str_pad($dcCount + 1, 4, '0', STR_PAD_LEFT);

        return view('admin_panel.direct_dc.create', compact('customers', 'warehouses', 'products', 'nextDcNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'dc_date' => 'required|date',
            'dc_number' => 'required|unique:delivery_challans,dc_number',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array',
            'loose_qty' => 'nullable|array',
            'color' => 'nullable|array',
            'price' => 'required|array',
            'warehouse_id' => 'required|exists:warehouses,id',
            'remarks' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $dc = DeliveryChallan::create([
                'sale_id' => null,
                'customer_id' => $validated['customer_id'],
                'dc_number' => $validated['dc_number'],
                'dc_date' => $validated['dc_date'],
                'status' => 'confirmed',
                'is_invoiced' => 0,
                'remarks' => $validated['remarks'] ?? null,
                'created_by' => auth()->id()
            ]);

            \App\Models\InvoiceSeries::incrementCounterForInvoice($validated['dc_number']);

            $warehouseId = $validated['warehouse_id'];
            $srMovements = [];

            foreach ($validated['product_id'] as $index => $productId) {
                $qty = (float) $validated['qty'][$index];
                $loose = isset($validated['loose_qty'][$index]) ? (float) $validated['loose_qty'][$index] : 0;
                $price = (float) $validated['price'][$index];
                
                if ($qty <= 0 && $loose <= 0) continue;

                $product = Product::find($productId);
                $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                
                // If the user inputs qty (boxes/kg) and loose (pcs/gm), total delivered qty in terms of pieces:
                // Actually, if it's kg/gm, loose is grams. So loose / 1000.
                // If it's boxes/pcs, loose is pieces. So loose / ppb.
                // It's safer to just store what's given. 
                // delivered_qty stores the main unit (Boxes/Kg).
                $mainQty = $qty + ($loose / $ppb);

                $colorVal = isset($validated['color'][$index]) ? $validated['color'][$index] : null;

                DeliveryChallanItem::create([
                    'delivery_challan_id' => $dc->id,
                    'sale_item_id' => null,
                    'product_id' => $productId,
                    'color' => $colorVal,
                    'warehouse_id' => $warehouseId,
                    'delivered_qty' => $mainQty,
                    'price' => $price,
                    'boxes' => $qty,
                    'loose_pieces' => $loose
                ]);

                $productMode = $product->size_mode;
                if ($productMode === 'by_kg' || $productMode === 'by_gm') {
                    $totalPieces = $qty + ($loose / 1000);
                    if ($colorVal) {
                        try {
                            $decoded = base64_decode($colorVal, true);
                            $vData = $decoded !== false ? json_decode($decoded, true) : null;
                            if (!is_array($vData)) {
                                $vData = is_string($colorVal) ? json_decode($colorVal, true) : $colorVal;
                            }
                            if (is_array($vData) && isset($vData['conv_factor']) && (float)$vData['conv_factor'] > 0) {
                                $totalPieces = $totalPieces * (float)$vData['conv_factor'];
                            }
                        } catch (\Exception $e) {}
                    }
                } elseif ($productMode === 'by_cartons' || $productMode === 'by_size') {
                    $totalPieces = ($qty * $ppb) + $loose;
                } else {
                    $totalPieces = $qty;
                }

                // Deduct Stock
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->total_pieces -= $totalPieces;
                    $stock->quantity = $stock->total_pieces / ($ppb > 0 ? $ppb : 1);
                    $stock->save();
                } else {
                    WarehouseStock::create([
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                        'total_pieces' => -$totalPieces,
                        'quantity' => -($totalPieces / ($ppb > 0 ? $ppb : 1)),
                        'price' => 0
                    ]);
                }

                // Log Movement
                $srMovements[] = [
                    'product_id' => $productId,
                    'type' => 'out',
                    'qty' => -$totalPieces,
                    'ref_type' => 'direct_dc',
                    'ref_id' => $dc->id,
                    'note' => 'Direct DC Out: ' . $dc->dc_number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($srMovements)) {
                DB::table('stock_movements')->insert($srMovements);
            }

            DB::commit();
            return redirect()->route('direct-dc.index')->with('success', 'Direct Delivery Challan created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating Direct DC: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $dc = DeliveryChallan::with(['items.product', 'customer', 'sale.customer_relation'])->findOrFail($id);
        if ($dc->is_invoiced) {
            return redirect()->route('direct-dc.index')->with('error', 'Cannot edit an invoiced DC.');
        }

        $customers = Customer::orderBy('customer_name')->get();
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $products = Product::orderBy('item_name')->get();

        return view('admin_panel.direct_dc.edit', compact('dc', 'customers', 'warehouses', 'products'));
    }

    public function update(Request $request, $id)
    {
        $dc = DeliveryChallan::with('items')->findOrFail($id);
        if ($dc->is_invoiced) {
            return redirect()->route('direct-dc.index')->with('error', 'Cannot edit an invoiced DC.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'dc_date' => 'required|date',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'qty' => 'required|array',
            'loose_qty' => 'nullable|array',
            'color' => 'nullable|array',
            'price' => 'required|array',
            'warehouse_id' => 'required|exists:warehouses,id',
            'remarks' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $dc->update([
                'customer_id' => $validated['customer_id'],
                'dc_date' => $validated['dc_date'],
                'remarks' => $validated['remarks'] ?? null
            ]);

            $warehouseId = $validated['warehouse_id'];
            $srMovements = [];

            // Simple approach: restore all stock from existing items, then process new ones
            foreach ($dc->items as $item) {
                $stock = WarehouseStock::where('warehouse_id', $item->warehouse_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();
                
                $productMode = $item->product ? $item->product->size_mode : 'by_pieces';
                $ppb = $item->product ? ($item->product->pieces_per_box > 0 ? $item->product->pieces_per_box : 1) : 1;
                $qty = (float) $item->boxes;
                $loose = (float) $item->loose_pieces;

                if ($productMode === 'by_kg' || $productMode === 'by_gm') {
                    $totalPieces = $qty + ($loose / 1000);
                    if ($item->color) {
                        try {
                            $decoded = base64_decode($item->color, true);
                            $vData = $decoded !== false ? json_decode($decoded, true) : null;
                            if (!is_array($vData)) {
                                $vData = is_string($item->color) ? json_decode($item->color, true) : $item->color;
                            }
                            if (is_array($vData) && isset($vData['conv_factor']) && (float)$vData['conv_factor'] > 0) {
                                $totalPieces = $totalPieces * (float)$vData['conv_factor'];
                            }
                        } catch (\Exception $e) {}
                    }
                } elseif ($productMode === 'by_cartons' || $productMode === 'by_size') {
                    $totalPieces = ($qty * $ppb) + $loose;
                } else {
                    $totalPieces = $qty;
                }

                if ($stock) {
                    $stock->total_pieces += $totalPieces;
                    $stock->quantity = $stock->total_pieces / $ppb;
                    $stock->save();
                }
                
                $srMovements[] = [
                    'product_id' => $item->product_id,
                    'type' => 'in',
                    'qty' => $totalPieces,
                    'ref_type' => 'direct_dc',
                    'ref_id' => $dc->id,
                    'note' => 'DC Edit Revert: ' . $dc->dc_number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $item->delete();
            }

            foreach ($validated['product_id'] as $index => $productId) {
                $qty = (float) $validated['qty'][$index];
                $loose = isset($validated['loose_qty'][$index]) ? (float) $validated['loose_qty'][$index] : 0;
                $price = (float) $validated['price'][$index];

                if ($qty <= 0 && $loose <= 0) continue;

                $product = Product::find($productId);
                $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
                $mainQty = $qty + ($loose / $ppb);
                $colorVal = isset($validated['color'][$index]) ? $validated['color'][$index] : null;

                $saleItemId = null;
                if ($dc->sale_id) {
                    $saleItem = \App\Models\SaleItem::where('sale_id', $dc->sale_id)
                        ->where('product_id', $productId)
                        ->first();
                    if ($saleItem) {
                        $saleItemId = $saleItem->id;
                    }
                }

                DeliveryChallanItem::create([
                    'delivery_challan_id' => $dc->id,
                    'sale_item_id' => $saleItemId,
                    'product_id' => $productId,
                    'color' => $colorVal,
                    'warehouse_id' => $warehouseId,
                    'delivered_qty' => $mainQty,
                    'price' => $price,
                    'boxes' => $qty,
                    'loose_pieces' => $loose
                ]);

                $productMode = $product->size_mode;
                if ($productMode === 'by_kg' || $productMode === 'by_gm') {
                    $totalPieces = $qty + ($loose / 1000);
                    if ($colorVal) {
                        try {
                            $decoded = base64_decode($colorVal, true);
                            $vData = $decoded !== false ? json_decode($decoded, true) : null;
                            if (!is_array($vData)) {
                                $vData = is_string($colorVal) ? json_decode($colorVal, true) : $colorVal;
                            }
                            if (is_array($vData) && isset($vData['conv_factor']) && (float)$vData['conv_factor'] > 0) {
                                $totalPieces = $totalPieces * (float)$vData['conv_factor'];
                            }
                        } catch (\Exception $e) {}
                    }
                } elseif ($productMode === 'by_cartons' || $productMode === 'by_size') {
                    $totalPieces = ($qty * $ppb) + $loose;
                } else {
                    $totalPieces = $qty;
                }

                // Deduct Stock
                $stock = WarehouseStock::where('warehouse_id', $warehouseId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->total_pieces -= $totalPieces;
                    $stock->quantity = $stock->total_pieces / ($ppb > 0 ? $ppb : 1);
                    $stock->save();
                } else {
                    WarehouseStock::create([
                        'warehouse_id' => $warehouseId,
                        'product_id' => $productId,
                        'total_pieces' => -$totalPieces,
                        'quantity' => -($totalPieces / ($ppb > 0 ? $ppb : 1)),
                        'price' => 0
                    ]);
                }

                // Log Movement
                $srMovements[] = [
                    'product_id' => $productId,
                    'type' => 'out',
                    'qty' => -$totalPieces,
                    'ref_type' => 'direct_dc',
                    'ref_id' => $dc->id,
                    'note' => 'Direct DC Out (Update): ' . $dc->dc_number,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($srMovements)) {
                DB::table('stock_movements')->insert($srMovements);
            }

            if ($dc->sale_id) {
                $sale = Sale::find($dc->sale_id);
                if ($sale) {
                    $sale->recalculateDeliveryStatus();
                }
            }

            DB::commit();
            return redirect()->route('direct-dc.index')->with('success', 'Direct Delivery Challan updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating Direct DC: ' . $e->getMessage());
        }
    }

    private function getItemDisplayData($item)
    {
        $product = $item->product;
        $variant = [];
        $colorData = $item->color;
        if (!empty($colorData)) {
            $b64 = base64_decode($colorData, true);
            if ($b64 !== false) $variant = json_decode($b64, true) ?: [];
            if (empty($variant)) $variant = json_decode($colorData, true) ?: [];
        }

        $sizeMode = optional($product)->size_mode ?? 'by_size';
        $vUnit = strtolower($variant['unit'] ?? optional(optional($product)->unit)->name ?? '');

        $dispQtyFactor = 1;
        $unitName = 'Pcs';

        if (in_array($sizeMode, ['by_kg', 'by_gm'])) {
            if (in_array($vUnit, ['pcs', 'pc', 'piece', 'pieces'])) {
                $unitName = 'Pcs';
                $wtConv = (float)($variant['conv_factor'] ?? optional($product)->pieces_per_box ?? 1);
                if ($wtConv <= 0) $wtConv = 1;
                $dispQtyFactor = 1 / $wtConv;
            } elseif (in_array($vUnit, ['gm', 'g'])) {
                $unitName = 'Gm';
                $dispQtyFactor = 1000;
            } else {
                $unitName = 'Kg';
                $dispQtyFactor = 1;
            }
        } elseif ($sizeMode === 'by_cartons') {
            $unitName = 'Ctn';
        } elseif ($sizeMode === 'by_boxes') {
            $unitName = 'Box';
        } else {
            $unitName = optional(optional($product)->unit)->name ?: 'Pcs';
        }

        if ((float)$item->boxes > 0) {
            $displayQty = (float)$item->boxes + ((float)$item->loose_pieces / (optional($product)->pieces_per_box > 0 ? $product->pieces_per_box : 1));
        } elseif ($item->sale_item_id && (float)$item->delivered_qty > 0) {
            $displayQty = (float)$item->delivered_qty * $dispQtyFactor;
        } else {
            $displayQty = (float)$item->delivered_qty;
        }

        $rate = (float)($item->price > 0 ? $item->price : optional($item->saleItem)->price ?? 0);
        $amount = round($displayQty * $rate, 2);

        return [
            'display_qty' => $displayQty,
            'unit' => $unitName,
            'rate' => $rate,
            'amount' => $amount,
            'disp_factor' => $dispQtyFactor,
            'conv_factor' => (float)($variant['conv_factor'] ?? 1),
            'size_mode' => $sizeMode,
        ];
    }

    public function consolidatePreview(Request $request)
    {
        $dcIds = $request->input('dc_ids');
        if (empty($dcIds) || !is_array($dcIds)) {
            if ($request->query('dc_ids')) {
                $dcIds = is_array($request->query('dc_ids')) ? $request->query('dc_ids') : explode(',', $request->query('dc_ids'));
            } elseif (session('consolidate_dc_ids')) {
                $dcIds = session('consolidate_dc_ids');
            } else {
                return redirect()->route('direct-dc.index')->with('error', 'No DCs selected. Please select Delivery Challan(s) to consolidate.');
            }
        }

        // Cache selected DC IDs in session to support page reload / refresh
        session(['consolidate_dc_ids' => $dcIds]);

        $dcs = DeliveryChallan::with(['items.product', 'items.saleItem', 'customer', 'sale.customer_relation'])->whereIn('id', $dcIds)->get();
        if ($dcs->isEmpty()) {
            return redirect()->route('direct-dc.index')->with('error', 'Selected DCs not found.');
        }

        $firstDc = $dcs->first();
        $customerId = $firstDc->customer_id ?: optional($firstDc->sale)->customer_id;

        foreach ($dcs as $dc) {
            $dcCustomerId = $dc->customer_id ?: optional($dc->sale)->customer_id;
            if ($dcCustomerId != $customerId) {
                return redirect()->route('direct-dc.index')->with('error', 'Only DCs from the SAME customer can be consolidated together.');
            }
            if ($dc->is_invoiced) {
                return redirect()->route('direct-dc.index')->with('error', "DC {$dc->dc_number} is already invoiced.");
            }
        }

        $distinctSaleIds = $dcs->pluck('sale_id')->filter()->unique();
        if ($distinctSaleIds->count() > 1) {
            $soNumbers = Sale::whereIn('id', $distinctSaleIds)->pluck('invoice_no')->filter()->implode(', ');
            return redirect()->route('direct-dc.index')->with('error', "Different Sales Orders (" . ($soNumbers ?: 'Multiple SOs') . ") ki Delivery Challans ko aik sath consolidate nahi kiya ja sakta! Sirf aik hi Sales Order ki DCs select karein.");
        }

        $hasSalesOrder = $dcs->contains(function($d) { return !empty($d->sale_id); });
        $hasDirectDc = $dcs->contains(function($d) { return empty($d->sale_id); });
        if ($hasSalesOrder && $hasDirectDc) {
            return redirect()->route('direct-dc.index')->with('error', "Direct Delivery Challan aur Sales Order ki Delivery Challan ko aik sath consolidate nahi kiya ja sakta.");
        }

        $customer = $firstDc->customer ?: (optional($firstDc->sale)->customer_relation ?: Customer::find($customerId));
        
        // Merge items based on product_id + warehouse_id + color
        $mergedItems = [];
        foreach ($dcs as $dc) {
            foreach ($dc->items as $item) {
                $itemData = $this->getItemDisplayData($item);
                $key = $item->product_id . '_' . $item->warehouse_id . '_' . ($item->color ?? 'base');
                if (!isset($mergedItems[$key])) {
                    $mergedItems[$key] = [
                        'product' => $item->product,
                        'product_id' => $item->product_id,
                        'warehouse_id' => $item->warehouse_id,
                        'color' => $item->color,
                        'price' => $itemData['rate'],
                        'unit' => $itemData['unit'],
                        'display_qty' => 0,
                        'delivered_qty' => 0,
                        'amount' => 0,
                        'disp_factor' => $itemData['disp_factor'],
                        'size_mode' => $itemData['size_mode'],
                    ];
                }
                $mergedItems[$key]['display_qty'] += $itemData['display_qty'];
                $mergedItems[$key]['delivered_qty'] += $itemData['display_qty'];
                $mergedItems[$key]['amount'] += $itemData['amount'];
            }
        }

        $seriesList = [
            'INV' => ['label' => 'Standard Invoice', 'next_no' => \App\Models\InvoiceSeries::generateNextNo('INV')],
            'TAX' => ['label' => 'Tax Invoice', 'next_no' => \App\Models\InvoiceSeries::generateNextNo('TAX')],
            'CO'  => ['label' => 'Company Invoice', 'next_no' => \App\Models\InvoiceSeries::generateNextNo('CO')],
        ];

        return view('admin_panel.direct_dc.consolidate_preview', compact('dcs', 'customer', 'mergedItems', 'dcIds', 'customerId', 'seriesList'));
    }

    public function consolidateIndex()
    {
        $customers = Customer::orderBy('customer_name')->get();
        return view('admin_panel.direct_dc.consolidate', compact('customers'));
    }

    public function fetchCustomerDCs($customerId)
    {
        $dcs = DeliveryChallan::with(['items.product', 'items.saleItem'])
            ->where(function($q) use ($customerId) {
                $q->where('customer_id', $customerId)
                  ->orWhereHas('sale', function($sq) use ($customerId) {
                      $sq->where('customer_id', $customerId);
                  });
            })
            ->where('is_invoiced', 0)
            ->get();
            
        // Map to simpler format for frontend
        $data = $dcs->map(function($dc) {
            $total = $dc->items->sum(function($item) {
                $itemData = $this->getItemDisplayData($item);
                return $itemData['amount'];
            });
            return [
                'id' => $dc->id,
                'dc_number' => $dc->dc_number,
                'dc_date' => \Carbon\Carbon::parse($dc->dc_date)->format('d M, Y'),
                'items_count' => $dc->items->count(),
                'total_amount' => number_format($total, 2, '.', '')
            ];
        });
        
        return response()->json($data);
    }

    public function consolidateStore(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'dc_ids' => 'required|array',
            'dc_ids.*' => 'required|exists:delivery_challans,id',
            'prefix' => 'nullable|string|in:INV,TAX,CO',
            'sale_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $dcs = DeliveryChallan::with(['items.product', 'items.saleItem', 'sale'])
                ->whereIn('id', $validated['dc_ids'])
                ->where(function($q) use ($validated) {
                    $q->where('customer_id', $validated['customer_id'])
                      ->orWhereHas('sale', function($sq) use ($validated) {
                          $sq->where('customer_id', $validated['customer_id']);
                      });
                })
                ->where('is_invoiced', 0)
                ->get();
                
            if ($dcs->isEmpty()) {
                return redirect()->back()->with('error', 'No valid un-invoiced DCs selected.');
            }

            $distinctSaleIds = $dcs->pluck('sale_id')->filter()->unique();
            if ($distinctSaleIds->count() > 1) {
                $soNumbers = Sale::whereIn('id', $distinctSaleIds)->pluck('invoice_no')->filter()->implode(', ');
                return redirect()->route('direct-dc.index')->with('error', "Different Sales Orders (" . ($soNumbers ?: 'Multiple SOs') . ") ki Delivery Challans ko aik sath consolidate nahi kiya ja sakta! Sirf aik hi Sales Order ki DCs select karein.");
            }

            $hasSalesOrder = $dcs->contains(function($d) { return !empty($d->sale_id); });
            $hasDirectDc = $dcs->contains(function($d) { return empty($d->sale_id); });
            if ($hasSalesOrder && $hasDirectDc) {
                return redirect()->route('direct-dc.index')->with('error', "Direct Delivery Challan aur Sales Order ki Delivery Challan ko aik sath consolidate nahi kiya ja sakta.");
            }

            // Create Master Sale Invoice
            $sale = new Sale();
            $sale->customer_id = $validated['customer_id'];
            $sale->sale_type = 'direct_sale';
            $sale->sale_status = 'posted';
            $sale->delivery_status = 'delivered'; // already delivered
            
            // Allow backdating invoice if sale_date is provided
            if ($request->filled('sale_date')) {
                $sale->created_at = \Carbon\Carbon::parse($request->sale_date)->format('Y-m-d H:i:s');
            }
            $invoiceDate = $sale->created_at ? $sale->created_at->format('Y-m-d') : now()->format('Y-m-d');

            // Assign Invoice Number according to selected prefix (INV, TAX, CO)
            $chosenPrefix = $request->input('prefix');
            $prefix = in_array(strtoupper($chosenPrefix), ['TAX', 'CO', 'INV']) ? strtoupper($chosenPrefix) : 'INV';
            $sale->invoice_no = \App\Models\InvoiceSeries::generateNextNo($prefix);
            
            // Collect items and aggregate totals
            $totalBillAmount = 0;
            $totalPieces = 0;
            $mergedItems = [];
            
            foreach ($dcs as $dc) {
                foreach ($dc->items as $item) {
                    $itemData = $this->getItemDisplayData($item);
                    $key = $item->product_id . '_' . $item->warehouse_id . '_' . ($item->color ?? 'base');
                    
                    if (!isset($mergedItems[$key])) {
                        $mergedItems[$key] = [
                            'product_id' => $item->product_id,
                            'warehouse_id' => $item->warehouse_id,
                            'display_qty' => 0,
                            'price' => $itemData['rate'],
                            'color' => $item->color,
                            'disp_factor' => $itemData['disp_factor'],
                            'size_mode' => $itemData['size_mode'],
                        ];
                    }
                    
                    $mergedItems[$key]['display_qty'] += $itemData['display_qty'];
                }
            }
            
            $saleItemsData = [];
            foreach ($mergedItems as $mi) {
                $displayQty = $mi['display_qty'];
                $rate = $mi['price'];
                $lineTotal = round($displayQty * $rate, 2);
                
                $product = \App\Models\Product::find($mi['product_id']);
                $sizeMode = $product ? $product->size_mode : $mi['size_mode'];
                $dispFactor = $mi['disp_factor'] > 0 ? $mi['disp_factor'] : 1;

                // For by_kg / by_gm, qty in sale_items is stored in base unit (Kg)
                if ($product && in_array($sizeMode, ['by_kg', 'by_gm'])) {
                    $storedQty = $displayQty / $dispFactor;
                } else {
                    $storedQty = $displayQty;
                }
                
                $saleItemsData[] = [
                    'product_id' => $mi['product_id'],
                    'warehouse_id' => $mi['warehouse_id'],
                    'color' => $mi['color'],
                    'qty' => $storedQty,
                    'total_pieces' => $storedQty,
                    'price' => $rate,
                    'discount_amount' => 0,
                    'discount_percent' => 0,
                    'total' => $lineTotal,
                    'delivered_qty' => $storedQty,
                ];
                
                $totalBillAmount += $lineTotal;
                $totalPieces += $displayQty;
            }

            $firstOriginalSaleId = $dcs->pluck('sale_id')->filter()->first();
            if ($firstOriginalSaleId) {
                $sale->parent_quotation_id = $firstOriginalSaleId;
                $sale->reference = $dcs->pluck('dc_number')->implode(', ');
            }

            $sale->total_bill_amount = $totalBillAmount;
            $sale->total_net = $totalBillAmount;
            $sale->total_items = $totalPieces;
            $sale->cash = 0;
            $sale->change = 0;
            $sale->save();

            \App\Models\InvoiceSeries::incrementCounterForInvoice($sale->invoice_no);

            // Create Sale Items
            foreach ($saleItemsData as $si) {
                $saleItem = new \App\Models\SaleItem($si);
                $saleItem->sale_id = $sale->id;
                $saleItem->save();
            }

            // Update DCs
            foreach ($dcs as $dc) {
                $originalSaleId = $dc->sale_id;
                if (!$dc->sale_id) {
                    $dc->sale_id = $sale->id;
                }
                if (!$dc->customer_id) {
                    $dc->customer_id = $validated['customer_id'];
                }
                $dc->invoice_id = $sale->id;
                $dc->is_invoiced = 1;
                $dc->save();

                if ($originalSaleId && $originalSaleId != $sale->id) {
                    $parentSale = Sale::find($originalSaleId);
                    if ($parentSale) {
                        $parentSale->recalculateDeliveryStatus();
                        $uninvoicedDcsCount = DeliveryChallan::where('sale_id', $parentSale->id)->where('is_invoiced', 0)->count();
                        if ($parentSale->delivery_status === 'delivered' && $uninvoicedDcsCount === 0) {
                            $parentSale->sale_status = 'posted';
                            $parentSale->save();
                        }
                    }
                }
            }

            // Post Ledger
            $balanceService = app(\App\Services\BalanceService::class);
            $custForVoucher = Customer::find($validated['customer_id']);
            if ($custForVoucher) {
                // 1. New Professional Ledger
                $balanceService->createSaleVoucher(
                    $custForVoucher,
                    $sale->total_net,
                    $sale->invoice_no,
                    $invoiceDate
                );
                
                // 2. Legacy CustomerLedger
                $ledger = \App\Models\CustomerLedger::where('customer_id', $custForVoucher->id)->latest('id')->first();
                $prev_bal = $ledger ? $ledger->closing_balance : ($custForVoucher->previous_balance ?? 0);
                $new_bal = $prev_bal + $sale->total_net;
                
                \App\Models\CustomerLedger::create([
                    'customer_id' => $custForVoucher->id,
                    'admin_or_user_id' => auth()->id() ?? 1,
                    'description' => 'Sale Invoice #'.$sale->invoice_no,
                    'previous_balance' => $prev_bal,
                    'closing_balance' => $new_bal,
                    'opening_balance' => 0,
                ]);
                
                // 3. Update Customer Master
                $custForVoucher->previous_balance = $new_bal;
                $custForVoucher->save();
            }

            DB::commit();
            session()->forget('consolidate_dc_ids');
            return redirect()->route('sale.index')->with('success', 'DCs successfully consolidated into Invoice: ' . $sale->invoice_no);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Consolidation failed: ' . $e->getMessage());
        }
    }
}
