<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Check if user is authorized for stock adjustments
     */
    private function checkPermission()
    {
        $user = auth()->user();
        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        if ($user->can('stock.adjust.view') || $user->can('stock.adjust.create')) {
            return true;
        }

        abort(403, 'Unauthorized action. You do not have permission to access Stock Adjustments.');
    }

    /**
     * Get warehouses permitted for current user
     */
    private function getPermittedWarehouses()
    {
        $user = auth()->user();

        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->can('warehouse.view')) {
            return Warehouse::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END, id ASC")->get();
        }

        if (isset($user->warehouse_id) && $user->warehouse_id) {
            return Warehouse::where('id', $user->warehouse_id)->get();
        }

        $userWarehouses = Warehouse::where('creater_id', $user->id)->get();
        if ($userWarehouses->count() > 0) {
            return $userWarehouses;
        }

        return Warehouse::orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END, id ASC")->get();
    }

    /**
     * Audit Log Index Page
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $permittedWarehouses = $this->getPermittedWarehouses();
        $permittedWarehouseIds = $permittedWarehouses->pluck('id')->toArray();

        $query = StockAdjustment::with(['user', 'warehouse', 'product'])
            ->whereIn('warehouse_id', $permittedWarehouseIds);

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('item_name', 'like', "%{$search}%")
                       ->orWhere('item_code', 'like', "%{$search}%");
                })->orWhere('variant_name', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $adjustments = $query->latest()->paginate(20)->appends($request->query());

        // Summary Stats
        $totalAdjustments = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)->count();
        $totalAdded       = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)->where('type', 'add')->sum('qty');
        $totalSubtracted  = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)->where('type', 'subtract')->sum('qty');
        $thisMonthCount   = StockAdjustment::whereIn('warehouse_id', $permittedWarehouseIds)
                                          ->whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->count();

        $products = Product::select('id', 'item_name', 'item_code', 'color', 'size_mode', 'pieces_per_box')->get();

        return view('admin_panel.stock_adjustment.index', [
            'adjustments'       => $adjustments,
            'totalAdjustments'  => $totalAdjustments,
            'totalAdded'        => $totalAdded,
            'totalSubtracted'   => $totalSubtracted,
            'thisMonthCount'    => $thisMonthCount,
            'warehouses'        => $permittedWarehouses,
            'products'          => $products,
        ]);
    }

    /**
     * Dedicated POS-Style Stock Adjustment Terminal Screen
     */
    public function create(Request $request)
    {
        $this->checkPermission();

        $warehouses = $this->getPermittedWarehouses();
        $categories = Category::all();
        $brands     = Brand::all();

        // Fetch products with variant details
        $products = Product::with(['category_relation', 'brand'])
            ->select('id', 'item_name', 'item_code', 'barcode_path', 'image', 'category_id', 'brand_id', 'color', 'size_mode', 'pieces_per_box')
            ->get();

        return view('admin_panel.stock_adjustment.create', compact('warehouses', 'categories', 'brands', 'products'));
    }

public function getProductVariants(Request $request, $productId)
    {
        $this->checkPermission();

        $product = Product::with('unit')->findOrFail($productId);
        $warehouseId = $request->query('warehouse_id');
        if (!$warehouseId) {
            $warehouseId = $this->getPermittedWarehouses()->first()->id ?? 1;
        }

        $stockPieces = 0;
        if ($warehouseId) {
            $whStock = WarehouseStock::where('warehouse_id', $warehouseId)->where('product_id', $productId)->first();
            $stockPieces = (float) ($whStock->total_pieces ?? 0);
        } else {
            $stockPieces = (float) WarehouseStock::where('product_id', $productId)->sum('total_pieces');
        }

        $ppb = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
        $sizeMode = $product->size_mode;

        // Default base unit
        $defaultUnit = $product->unit->name ?? match($sizeMode) {
            'by_kg'      => 'Kg',
            'by_meter'   => 'Meter',
            'by_feet'    => 'Ft',
            'by_cartons' => 'Carton',
            default      => 'Pcs'
        };

        $variants = [];
        if ($product->color) {
            try {
                $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                if (is_array($parsed) && count($parsed) > 0) {
                    $rawList = isset($parsed['name']) || isset($parsed['color']) ? (isset($parsed[0]) ? $parsed : [$parsed]) : array_values($parsed);

                    foreach ($rawList as $idx => $v) {
                        $vName  = $v['name'] ?? $v['variant_name'] ?? $product->item_name;
                        $vSize  = $v['size'] ?? $v['variant_size'] ?? '-';
                        $vColor = $v['color'] ?? $v['variant_color'] ?? '-';

                        // 1. Calculate Conversion Factor
                        $convFactor = 1.0;
                        if (isset($v['conv_factor']) && (float)$v['conv_factor'] > 0) {
                            $convFactor = (float) $v['conv_factor'];
                        } elseif (isset($v['weight_per_piece']) && (float)$v['weight_per_piece'] > 0) {
                            if ($sizeMode === 'by_feet') {
                                $convFactor = (float) $v['weight_per_piece'] / 12.0;
                            } elseif ($sizeMode === 'by_kg' || $sizeMode === 'by_meter') {
                                $convFactor = (float) $v['weight_per_piece'] / 1000.0;
                            }
                        } elseif ($sizeMode === 'by_cartons') {
                            $convFactor = $ppb;
                        }

                        // 2. Identify if base variant
                        $isBase = !empty($v['is_base_variant']) || (in_array($sizeMode, ['by_kg', 'by_meter', 'by_feet']) && $convFactor == 1.0);

                        // 3. Determine variant unit label
                        if (!empty($v['unit'])) {
                            $vUnit = trim($v['unit']);
                        } elseif ($sizeMode === 'by_kg') {
                            $vUnit = $isBase ? 'Kg' : 'Pcs';
                        } elseif ($sizeMode === 'by_meter') {
                            $vUnit = $isBase ? 'Meter' : 'Pcs';
                        } elseif ($sizeMode === 'by_feet') {
                            $vUnit = $isBase ? 'Ft' : 'Pcs';
                        } elseif ($sizeMode === 'by_cartons') {
                            $vUnit = ($convFactor == $ppb) ? 'Carton' : 'Pcs';
                        } else {
                            $vUnit = $defaultUnit;
                        }

                        // 4. Calculate live warehouse stock converted for this variant
                        if (in_array($sizeMode, ['by_kg', 'by_meter', 'by_feet'])) {
                            $vCurrentStock = ($convFactor > 0) ? round($stockPieces / $convFactor, 2) : round($stockPieces, 2);
                        } elseif ($sizeMode === 'by_cartons') {
                            if (strtolower($vUnit) === 'carton' || strtolower($vUnit) === 'box' || $convFactor == $ppb) {
                                $vCurrentStock = round($stockPieces / $ppb, 2);
                            } else {
                                $vCurrentStock = round($stockPieces, 2);
                            }
                        } else {
                            if ($convFactor > 0 && $convFactor != 1.0) {
                                $vCurrentStock = round($stockPieces / $convFactor, 2);
                            } else {
                                if (isset($v['stock']) && !in_array($sizeMode, ['by_kg', 'by_meter', 'by_feet', 'by_cartons', 'by_pieces'])) {
                                    $vCurrentStock = (float)$v['stock'];
                                } else {
                                    $vCurrentStock = round($stockPieces, 2);
                                }
                            }
                        }

                        // Step precision for HTML input
                        $step = (in_array(strtolower($vUnit), ['pcs', 'pc', 'piece', 'pieces']) && $convFactor == 1.0) ? 1 : 0.01;

                        // Display label
                        $metaParts = [];
                        if ($vSize !== '-' && $vSize !== '') $metaParts[] = "Size: {$vSize}";
                        if ($vColor !== '-' && $vColor !== '') $metaParts[] = "Color: {$vColor}";
                        $metaDesc = count($metaParts) > 0 ? ' (' . implode(', ', $metaParts) . ')' : '';

                        $variants[] = [
                            'index'           => $idx,
                            'variant_key'     => ($vName . '|' . $vSize . '|' . $vColor),
                            'name'            => $vName,
                            'size'            => $vSize,
                            'color'           => $vColor,
                            'unit'            => $vUnit,
                            'conv_factor'     => $convFactor,
                            'is_base_variant' => $isBase ? 1 : 0,
                            'current_stock'   => $vCurrentStock,
                            'step'            => $step,
                            'display_label'   => $vName . $metaDesc,
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        $defaultStep = in_array(strtolower($defaultUnit), ['pcs', 'pc', 'piece', 'pieces']) ? 1 : 0.01;

        return response()->json([
            'success'        => true,
            'product_id'     => $product->id,
            'product_name'   => $product->item_name,
            'item_code'      => $product->item_code,
            'size_mode'      => $sizeMode,
            'unit'           => $defaultUnit,
            'step'           => $defaultStep,
            'total_stock'    => round($stockPieces, 2),
            'has_variants'   => count($variants) > 0,
            'variants'       => $variants
        ]);
    }

    /**
     * Store Single Adjustment
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        if (!$request->filled('warehouse_id')) {
            $firstWh = $this->getPermittedWarehouses()->first();
            if ($firstWh) {
                $request->merge(['warehouse_id' => $firstWh->id]);
            }
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'type'         => 'required|in:add,subtract,set',
            'qty'          => 'required|numeric|min:0',
            'reason'       => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $this->processSingleAdjustment([
                    'warehouse_id' => $request->warehouse_id,
                    'product_id'   => $request->product_id,
                    'variant_key'  => $request->variant_key,
                    'variant_name' => $request->variant_name,
                    'type'         => $request->type,
                    'qty'          => (float) $request->qty,
                    'reason'       => $request->reason,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stock adjustment processed successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store Multi-Item / Multi-Variant Batch Adjustment
     */
    public function storeBatch(Request $request)
    {
        $this->checkPermission();

        if (!$request->filled('warehouse_id')) {
            $firstWh = $this->getPermittedWarehouses()->first();
            if ($firstWh) {
                $request->merge(['warehouse_id' => $firstWh->id]);
            }
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type'       => 'required|in:add,subtract,set',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'global_reason'      => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $warehouseId   = $request->warehouse_id;
                $globalReason  = $request->global_reason;

                foreach ($request->items as $item) {
                    $itemReason = !empty($item['reason']) ? $item['reason'] : $globalReason;

                    $this->processSingleAdjustment([
                        'warehouse_id' => $warehouseId,
                        'product_id'   => $item['product_id'],
                        'variant_key'  => $item['variant_key'] ?? null,
                        'variant_name' => $item['variant_name'] ?? null,
                        'type'         => $item['type'],
                        'qty'          => (float) $item['qty'],
                        'reason'       => $itemReason,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($request->items) . ' stock adjustment(s) saved successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process batch adjustment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Private helper to execute a single stock adjustment
     */
private function processSingleAdjustment(array $data)
    {
        $product = Product::with('unit')->where('id', $data['product_id'])->lockForUpdate()->first();
        if (!$product) {
            throw new \Exception("Product #{$data['product_id']} not found.");
        }

        $warehouseStock = WarehouseStock::firstOrCreate(
            [
                'warehouse_id' => $data['warehouse_id'],
                'product_id'   => $data['product_id'],
            ],
            [
                'quantity'     => 0,
                'total_pieces' => 0,
            ]
        );

        $sizeMode = $product->size_mode;
        $ppb = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
        $defaultUnit = $product->unit->name ?? match($sizeMode) {
            'by_kg'      => 'Kg',
            'by_meter'   => 'Meter',
            'by_feet'    => 'Ft',
            'by_cartons' => 'Carton',
            default      => 'Pcs'
        };

        $variantKey  = $data['variant_key'] ?? null;
        $variantName = $data['variant_name'] ?? null;
        $inputQty    = (float) $data['qty'];
        $type        = $data['type']; // 'add', 'subtract', 'set'

        // Determine variant's conv_factor and unit
        $convFactor = 1.0;
        $unit = $defaultUnit;
        $parsedVariants = [];
        $matchedIndex = null;

        if ($product->color) {
            try {
                $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                if (is_array($parsed) && count($parsed) > 0) {
                    $rawList = isset($parsed['name']) || isset($parsed['color']) ? (isset($parsed[0]) ? $parsed : [$parsed]) : array_values($parsed);
                    $parsedVariants = $rawList;

                    // First pass: try matching by exact variant_key (which includes unit)
                    foreach ($parsedVariants as $idx => $v) {
                        $vName  = $v['name'] ?? $product->item_name;
                        $vSize  = $v['size'] ?? '-';
                        $vColor = $v['color'] ?? '-';

                        // Pre-calculate convFactor & unit for this variant
                        $vCf = 1.0;
                        if (isset($v['conv_factor']) && (float)$v['conv_factor'] > 0) {
                            $vCf = (float)$v['conv_factor'];
                        } elseif (isset($v['weight_per_piece']) && (float)$v['weight_per_piece'] > 0) {
                            if ($sizeMode === 'by_feet') {
                                $vCf = (float)$v['weight_per_piece'] / 12.0;
                            } elseif ($sizeMode === 'by_kg' || $sizeMode === 'by_meter') {
                                $vCf = (float)$v['weight_per_piece'] / 1000.0;
                            }
                        } elseif ($sizeMode === 'by_cartons') {
                            $vCf = $ppb;
                        }

                        $isBase = !empty($v['is_base_variant']) || (in_array($sizeMode, ['by_kg', 'by_meter', 'by_feet']) && $vCf == 1.0);
                        if (!empty($v['unit'])) {
                            $vU = trim($v['unit']);
                        } elseif ($sizeMode === 'by_kg') {
                            $vU = $isBase ? 'Kg' : 'Pcs';
                        } elseif ($sizeMode === 'by_meter') {
                            $vU = $isBase ? 'Meter' : 'Pcs';
                        } elseif ($sizeMode === 'by_feet') {
                            $vU = $isBase ? 'Ft' : 'Pcs';
                        } elseif ($sizeMode === 'by_cartons') {
                            $vU = ($vCf == $ppb) ? 'Carton' : 'Pcs';
                        } else {
                            $vU = $defaultUnit;
                        }

                        $vKeyWithUnit = "{$vName}|{$vSize}|{$vColor}|{$vU}";
                        $vKeyWithoutUnit = "{$vName}|{$vSize}|{$vColor}";

                        if ($variantKey && ($vKeyWithUnit === $variantKey || $vKeyWithoutUnit === $variantKey)) {
                            $matchedIndex = $idx;
                            $convFactor   = $vCf;
                            $unit         = $vU;
                            break;
                        } elseif ($variantName && $vName === $variantName) {
                            $matchedIndex = $idx;
                            $convFactor   = $vCf;
                            $unit         = $vU;
                            // continue in case an exact match appears later
                        }
                    }
                }
            } catch (\Exception $e) {}
        }

        // Special check if standard product is by_cartons
        if ($sizeMode === 'by_cartons' && empty($variantKey) && (strtolower($unit) === 'carton' || strtolower($unit) === 'box')) {
            $convFactor = $ppb;
        }

        if ($convFactor <= 0) {
            $convFactor = 1.0;
        }

        $currentBaseStock = (float) $warehouseStock->total_pieces;

        // Current stock in the variant's own unit
        $oldVariantStock = ($convFactor > 0) ? round($currentBaseStock / $convFactor, 2) : round($currentBaseStock, 2);

        // Calculate Delta in Base Units (e.g. Kg, Ft, Meter, Carton Pieces)
        // 1 Variant Unit = $convFactor Base Units
        $deltaBase = 0;
        $newBaseStock = $currentBaseStock;

        if ($type === 'add') {
            $deltaBase = $inputQty * $convFactor;
            $newBaseStock = $currentBaseStock + $deltaBase;
        } elseif ($type === 'subtract') {
            $deductBase = $inputQty * $convFactor;
            $deltaBase = -1.0 * min(max(0, $currentBaseStock), $deductBase);
            $newBaseStock = max(0, $currentBaseStock + $deltaBase);
        } else { // 'set'
            $targetBaseStock = max(0, $inputQty * $convFactor);
            $deltaBase = $targetBaseStock - $currentBaseStock;
            $newBaseStock = $targetBaseStock;
        }

        // New stock in the variant's own unit
        if ($type === 'set') {
            $newVariantStock = $inputQty;
        } else {
            $newVariantStock = ($convFactor > 0) ? round($newBaseStock / $convFactor, 2) : round($newBaseStock, 2);
        }

        // Update variant stock in product JSON
        if (count($parsedVariants) > 0) {
            try {
                if (in_array($sizeMode, ['by_kg', 'by_feet', 'by_meter', 'by_cartons'])) {
                    // All variants are different units of the same base stock -> update all
                    foreach ($parsedVariants as &$pv) {
                        $cf = (float)($pv['conv_factor'] ?? 0);
                        if ($cf <= 0) {
                            if ($sizeMode === 'by_feet' && !empty($pv['weight_per_piece'])) {
                                $cf = (float)$pv['weight_per_piece'] / 12.0;
                            } elseif (($sizeMode === 'by_kg' || $sizeMode === 'by_meter') && !empty($pv['weight_per_piece'])) {
                                $cf = (float)$pv['weight_per_piece'] / 1000.0;
                            } elseif ($sizeMode === 'by_cartons') {
                                $cf = $ppb;
                            } else {
                                $cf = 1.0;
                            }
                        }
                        $pv['stock'] = round($newBaseStock / ($cf > 0 ? $cf : 1.0), 2);
                    }
                    unset($pv);
                } else {
                    // Independent variants
                    if ($matchedIndex !== null) {
                        $vOld = (float)($parsedVariants[$matchedIndex]['stock'] ?? 0);
                        if ($type === 'add') {
                            $parsedVariants[$matchedIndex]['stock'] = $vOld + $inputQty;
                        } elseif ($type === 'subtract') {
                            $parsedVariants[$matchedIndex]['stock'] = max(0, $vOld - $inputQty);
                        } else {
                            $parsedVariants[$matchedIndex]['stock'] = max(0, $inputQty);
                        }
                    }
                }

                $product->color = json_encode($parsedVariants);
                $product->save();
            } catch (\Exception $e) {}
        }

        // Update WarehouseStock
        $warehouseStock->total_pieces = $newBaseStock;
        if ($ppb > 1 && in_array($sizeMode, ['by_cartons', 'by_size'])) {
            $warehouseStock->quantity = round($warehouseStock->total_pieces / $ppb, 2);
        } else {
            $warehouseStock->quantity = $warehouseStock->total_pieces;
        }
        $warehouseStock->remarks = 'Adjusted via Stock Adjustment module';
        $warehouseStock->save();

        // Log Stock Movement
        $noteUnitInfo = "{$inputQty} {$unit}";
        if ($convFactor != 1.0) {
            $noteUnitInfo .= " = " . round(abs($deltaBase), 4) . " " . $defaultUnit;
        }
        $note = "Warehouse #{$data['warehouse_id']} | " 
              . ($variantName ? "Variant: {$variantName} | " : '')
              . "Adjustment: {$noteUnitInfo} ({$type}) | "
              . "Stock: " . round($currentBaseStock, 3) . " -> " . round($newBaseStock, 3) . " | "
              . "Reason: " . $data['reason'];

        DB::table('stock_movements')->insert([
            'product_id'   => $product->id,
            'type'         => 'adjustment',
            'qty'          => round($deltaBase, 4),
            'ref_type'     => 'STOCK_ADJUSTMENT',
            'ref_id'       => null,
            'note'         => substr($note, 0, 250),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Create StockAdjustment audit record
        $adjVariantDisplay = $variantName ?: ($product->item_name . " ({$unit})");
        StockAdjustment::create([
            'user_id'      => auth()->id(),
            'warehouse_id' => $data['warehouse_id'],
            'product_id'   => $product->id,
            'variant_key'  => $variantKey,
            'variant_name' => $adjVariantDisplay,
            'type'         => $data['type'],
            'qty'          => $inputQty,
            'old_stock'    => $oldVariantStock,
            'new_stock'    => $newVariantStock,
            'reason'       => $data['reason'] . ($convFactor != 1.0 ? " [Base: " . ($deltaBase >= 0 ? '+' : '') . round($deltaBase, 3) . " {$defaultUnit}]" : ''),
        ]);
    }
}
