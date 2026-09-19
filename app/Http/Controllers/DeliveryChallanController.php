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
            $baseNo = $sale->invoice_no ?: ('SO-' . $sale->id);
            $dc = DeliveryChallan::create([
                'sale_id' => $saleId,
                'dc_number' => $baseNo . '-DC' . str_pad($dcCount, 2, '0', STR_PAD_LEFT),
                'dc_date' => now()->format('Y-m-d'),
                'status' => 'confirmed', // We confirm it immediately as per plan
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

                        if (!$stock) {
                            throw new \Exception("No stock found in warehouse for {$item->product_name}.");
                        }

                        if ($stock->total_pieces < $deliveryQty) {
                            throw new \Exception("Insufficient stock in warehouse for {$item->product_name}. Available: {$stock->total_pieces}, Required: {$deliveryQty}.");
                        }

                        // Update Stock
                        $stock->total_pieces -= $deliveryQty;
                        $stock->quantity = $stock->total_pieces / $ppb;
                        $stock->save();

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
                        'warehouse_id' => $warehouseId,
                        'delivered_qty' => $deliveryQty,
                    ]);

                    // Update Sale Item
                    $item->delivered_qty += $deliveryQty;
                    $item->save();
                    $anyDelivered = true;
                }

                if ($item->remaining_qty > 0) {
                    $allDelivered = false;
                }
            }

            // Update Sale Status
            if ($allDelivered) {
                $sale->delivery_status = 'delivered';
            } elseif ($anyDelivered || $sale->delivery_status == 'partial') {
                $sale->delivery_status = 'partial';
            }
            $sale->save();

            DB::commit();

            return redirect()->route('sale.index')->with('success', 'Delivery Challan created successfully and stock deducted.');
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
}
