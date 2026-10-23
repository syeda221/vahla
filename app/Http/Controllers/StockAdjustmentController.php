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
            return Warehouse::orderBy('warehouse_name')->get();
        }

        if (isset($user->warehouse_id) && $user->warehouse_id) {
            return Warehouse::where('id', $user->warehouse_id)->get();
        }

        $userWarehouses = Warehouse::where('creater_id', $user->id)->get();
        if ($userWarehouses->count() > 0) {
            return $userWarehouses;
        }

        return Warehouse::limit(1)->get();
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

    /**
     * Format packaging quantity according to packaging units (Carton / Box / Pcs)
     */
    public function formatPackagingQuantity($pieces, $product, $ppb = null, $vUnit = null)
    {
        $pieces = (float)$pieces;
        $ppb = (float)($ppb > 0 ? $ppb : (($product->pieces_per_box ?? 0) > 0 ? $product->pieces_per_box : 1));
        $sizeMode = $product->size_mode ?? 'by_pieces';
        $isPieceOnly = ($sizeMode === 'by_pieces' || $ppb <= 1 || strtolower($vUnit ?? '') === 'pcs');

        if (!$isPieceOnly && in_array($sizeMode, ['by_cartons', 'by_bandal'])) {
            $ctnLbl = ($sizeMode === 'by_bandal') ? 'Bndl' : 'Ctn';
            $cartons = (int) floor($pieces / $ppb);
            $loose   = (int) round($pieces - ($cartons * $ppb));
            $ppbText = "({$ppb} pcs/" . strtolower($ctnLbl) . ")";

            if ($cartons > 0 && $loose > 0) {
                $mainText = "{$cartons} {$ctnLbl} + {$loose} Pcs";
            } elseif ($cartons > 0) {
                $mainText = "{$cartons} {$ctnLbl}";
            } else {
                $mainText = "{$loose} Pcs";
            }

            return [
                'main'          => $mainText,
                'sub'           => $ppbText,
                'full'          => "{$mainText} {$ppbText}",
                'cartons'       => $cartons,
                'loose'         => $loose,
                'total_pieces'  => $pieces,
                'unit_label'    => $ctnLbl,
                'ppb'           => $ppb,
                'is_carton'     => true,
            ];
        } elseif (!$isPieceOnly && $sizeMode === 'by_size') {
            $boxes = (int) floor($pieces / $ppb);
            $loose = (int) round($pieces - ($boxes * $ppb));
            $ppbText = "({$ppb} pcs/box)";

            if ($boxes > 0 && $loose > 0) {
                $mainText = "{$boxes} Box + {$loose} Pcs";
            } elseif ($boxes > 0) {
                $mainText = "{$boxes} Boxes";
            } else {
                $mainText = "{$loose} Pcs";
            }

            return [
                'main'          => $mainText,
                'sub'           => $ppbText,
                'full'          => "{$mainText} {$ppbText}",
                'cartons'       => $boxes,
                'loose'         => $loose,
                'total_pieces'  => $pieces,
                'unit_label'    => 'Box',
                'ppb'           => $ppb,
                'is_carton'     => true,
            ];
        } else {
            $unitName = $vUnit ?: ($product->unit?->name ?? 'Pcs');
            $unitDisplay = in_array(strtolower($unitName), ['pcs', 'piece', 'pieces']) ? 'Pcs' : $unitName;
            $pcsFormatted = (fmod($pieces, 1) !== 0.0) ? number_format($pieces, 2) : (int)$pieces;
            $mainText = "{$pcsFormatted} {$unitDisplay}";

            return [
                'main'          => $mainText,
                'sub'           => null,
                'full'          => $mainText,
                'cartons'       => 0,
                'loose'         => $pieces,
                'total_pieces'  => $pieces,
                'unit_label'    => $unitDisplay,
                'ppb'           => 1,
                'is_carton'     => false,
            ];
        }
    }

    /**
     * AJAX: Get all products with current warehouse stock and carton formatting
     */
    public function getWarehouseProducts(Request $request)
    {
        $this->checkPermission();
        $warehouseId = $request->query('warehouse_id');
        if (!$warehouseId) {
            $warehouseId = $this->getPermittedWarehouses()->first()->id ?? 1;
        }

        $whStocks = WarehouseStock::where('warehouse_id', $warehouseId)->get()->keyBy('product_id');
        $products = Product::all();

        $result = [];
        foreach ($products as $p) {
            $ws = $whStocks->get($p->id);
            $pieces = (float)($ws->total_pieces ?? 0);
            $fmt = $this->formatPackagingQuantity($pieces, $p);

            $result[$p->id] = [
                'product_id'   => $p->id,
                'total_pieces' => $pieces,
                'formatted'    => $fmt,
                'in_stock'     => $pieces > 0,
            ];
        }

        return response()->json([
            'success'   => true,
            'warehouse' => $warehouseId,
            'stocks'    => $result,
        ]);
    }

    public function getProductVariants(Request $request, $productId)
    {
        $this->checkPermission();

        $product = Product::findOrFail($productId);
        $warehouseId = $request->query('warehouse_id');
        if (!$warehouseId) {
            $warehouseId = $this->getPermittedWarehouses()->first()->id ?? 1;
        }

        $whStock = WarehouseStock::where('warehouse_id', $warehouseId)->where('product_id', $productId)->first();
        $totalWhPieces = (float) ($whStock->total_pieces ?? 0);
        $ppb = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
        $sizeMode = $product->size_mode ?? 'by_pieces';

        $decodedVariants = [];
        if (!empty($product->color)) {
            $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
            if (is_array($parsed) && count($parsed) > 0) {
                $decodedVariants = $parsed;
            }
        }

        $variantMatches = function ($rawColor, $targetVariantName) {
            if (!$targetVariantName) return true;
            if (empty($rawColor)) return false;
            $raw = $rawColor;
            $b64 = base64_decode($raw, true);
            if ($b64) $raw = $b64;
            $arr = json_decode($raw, true);
            if (!is_array($arr)) return false;
            return ($arr['name'] ?? null) === $targetVariantName;
        };

        $variants = [];
        if (count($decodedVariants) > 1) {
            $opStocks = \App\Models\OpeningStock::where('warehouse_id', $warehouseId)->where('product_id', $productId)->get();
            $saleItems = DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->where('sales.sale_status', '!=', 'cancelled')
                ->where('sale_items.warehouse_id', $warehouseId)
                ->where('sale_items.product_id', $productId)
                ->select('sale_items.total_pieces', 'sale_items.color')->get();

            $saleReturnItems = DB::table('sale_return_items')
                ->where('warehouse_id', $warehouseId)->where('product_id', $productId)->get();

            $purchaseItems = DB::table('purchase_items')->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
                ->whereNull('purchases.deleted_at')
                ->where('purchases.warehouse_id', $warehouseId)
                ->where('purchase_items.product_id', $productId)->get();

            $transfersOut = DB::table('stock_transfers')
                ->where('from_warehouse_id', $warehouseId)->where('product_id', $productId)->get();

            $transfersIn = DB::table('stock_transfers')
                ->where('to_warehouse_id', $warehouseId)->where('to_shop', false)->where('product_id', $productId)->get();

            $adjustments = DB::table('stock_adjustments')
                ->where('warehouse_id', $warehouseId)->where('product_id', $productId)->get();

            foreach ($decodedVariants as $dv) {
                $vName = $dv['name'] ?? $product->item_name;
                $vSize = $dv['size'] ?? '-';
                $vColor = $dv['color'] ?? '-';
                $vKey = $vName . '|' . $vSize . '|' . $vColor;
                $vUnit = $dv['unit'] ?? null;
                $vPpb = (!empty($dv['conv_factor']) && (float)$dv['conv_factor'] > 0) ? (float)$dv['conv_factor'] : $ppb;

                $op = $opStocks->first(function($o) use ($vKey, $vName) {
                    return ($o->variant_key === $vKey) || ($o->variant_name === $vName);
                });
                $opPieces = $op ? (float)$op->total_pieces : 0;

                $vSales = 0;
                foreach ($saleItems as $si) {
                    if ($variantMatches($si->color, $vName)) $vSales += (float)$si->total_pieces;
                }

                $vReturns = 0;
                foreach ($saleReturnItems as $ri) {
                    if ($variantMatches($ri->color, $vName)) {
                        $vReturns += (float)($ri->qty > 0 ? $ri->qty : ((float)$ri->boxes * $vPpb));
                    }
                }

                $vPurchases = 0;
                foreach ($purchaseItems as $pi) {
                    if ($variantMatches($pi->color, $vName)) {
                        $ppbItem = (float)($pi->pieces_per_box > 0 ? $pi->pieces_per_box : $vPpb);
                        $pPieces = ((float)$pi->boxes_qty * $ppbItem) + (float)$pi->loose_qty;
                        if ($pPieces <= 0) $pPieces = (float)$pi->qty;
                        $vPurchases += $pPieces;
                    }
                }

                $vTransfersOutPieces = (float) ($transfersOut->filter(function($t) use ($vKey, $vName) {
                    return empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName;
                })->sum('quantity')) * $vPpb;

                $vTransfersInPieces = (float) ($transfersIn->filter(function($t) use ($vKey, $vName) {
                    return empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName;
                })->sum('quantity')) * $vPpb;

                $vAdj = (float) $adjustments->filter(function($a) use ($vKey, $vName) {
                    return empty($a->variant_key) || $a->variant_key === $vKey || $a->variant_name === $vName;
                })->sum(function($a) {
                    return (float)$a->new_stock - (float)$a->old_stock;
                });

                $livePieces = max(0, $opPieces + $vPurchases - $vSales + $vReturns + $vTransfersInPieces - $vTransfersOutPieces + $vAdj);
                $fmt = $this->formatPackagingQuantity($livePieces, $product, $vPpb, $vUnit);

                $variants[] = [
                    'variant_key'   => $vKey,
                    'name'          => $vName,
                    'size'          => $vSize,
                    'color'         => $vColor,
                    'current_stock' => $livePieces,
                    'formatted'     => $fmt,
                    'display_label' => "{$vName} (Size: {$vSize}, Color: {$vColor})"
                ];
            }
        } else {
            $vName = $decodedVariants[0]['name'] ?? $product->item_name;
            $vSize = $decodedVariants[0]['size'] ?? '-';
            $vColor = $decodedVariants[0]['color'] ?? '-';
            $vKey = $vName . '|' . $vSize . '|' . $vColor;
            $vUnit = $decodedVariants[0]['unit'] ?? null;
            $vPpb = (!empty($decodedVariants[0]['conv_factor']) && (float)$decodedVariants[0]['conv_factor'] > 0) ? (float)$decodedVariants[0]['conv_factor'] : $ppb;

            $fmt = $this->formatPackagingQuantity($totalWhPieces, $product, $vPpb, $vUnit);
            $variants[] = [
                'variant_key'   => $vKey,
                'name'          => $vName,
                'size'          => $vSize,
                'color'         => $vColor,
                'current_stock' => $totalWhPieces,
                'formatted'     => $fmt,
                'display_label' => "{$vName}"
            ];
        }

        $overallFmt = $this->formatPackagingQuantity($totalWhPieces, $product);

        return response()->json([
            'success'               => true,
            'product_id'            => $product->id,
            'product_name'          => $product->item_name,
            'item_code'             => $product->item_code,
            'size_mode'             => $sizeMode,
            'pieces_per_box'        => $ppb,
            'total_stock'           => $totalWhPieces,
            'formatted_total_stock' => $overallFmt,
            'has_variants'          => count($decodedVariants) > 1,
            'variants'              => $variants
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
     * Helper to calculate the accurate live stock in pieces for a specific variant & warehouse
     */
    public function getVariantLivePieces(Product $product, $warehouseId, $variantKey = null, $variantName = null)
    {
        $whStock = WarehouseStock::where('warehouse_id', $warehouseId)->where('product_id', $product->id)->first();
        $totalWhPieces = (float) ($whStock->total_pieces ?? 0);
        $basePpb = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);

        $decodedVariants = [];
        if (!empty($product->color)) {
            $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
            if (is_array($parsed) && count($parsed) > 0) {
                $decodedVariants = $parsed;
            }
        }

        if (count($decodedVariants) <= 1) {
            return $totalWhPieces;
        }

        $vPpb = $basePpb;
        $targetDv = null;
        foreach ($decodedVariants as $dv) {
            $dvName = $dv['name'] ?? $product->item_name;
            $dvSize = $dv['size'] ?? '-';
            $dvColor = $dv['color'] ?? '-';
            $dvKey = $dvName . '|' . $dvSize . '|' . $dvColor;

            if (($variantKey && $dvKey === $variantKey) || ($variantName && $dvName === $variantName)) {
                $targetDv = $dv;
                if (!empty($dv['conv_factor']) && (float)$dv['conv_factor'] > 0) {
                    $vPpb = (float)$dv['conv_factor'];
                }
                break;
            }
        }

        $vName = $variantName ?: ($targetDv['name'] ?? $product->item_name);
        $vKey  = $variantKey ?: ($vName . '|' . ($targetDv['size'] ?? '-') . '|' . ($targetDv['color'] ?? '-'));

        $variantMatches = function ($rawColor, $targetVariantName) {
            if (!$targetVariantName) return true;
            if (empty($rawColor)) return false;
            $raw = $rawColor;
            $b64 = base64_decode($raw, true);
            if ($b64) $raw = $b64;
            $arr = json_decode($raw, true);
            if (!is_array($arr)) return false;
            return ($arr['name'] ?? null) === $targetVariantName;
        };

        // 1. Opening stock
        $op = \App\Models\OpeningStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $product->id)
            ->where(function($q) use ($vKey, $vName) {
                $q->where('variant_key', $vKey)->orWhere('variant_name', $vName);
            })->first();
        $opPieces = $op ? (float)$op->total_pieces : 0;

        // 2. Sales
        $saleItems = DB::table('sale_items')->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.sale_status', '!=', 'cancelled')
            ->where('sale_items.warehouse_id', $warehouseId)
            ->where('sale_items.product_id', $product->id)
            ->select('sale_items.total_pieces', 'sale_items.color')->get();

        $vSales = 0;
        foreach ($saleItems as $si) {
            if ($variantMatches($si->color, $vName)) $vSales += (float)$si->total_pieces;
        }

        // 3. Sale Returns
        $saleReturnItems = DB::table('sale_return_items')
            ->where('warehouse_id', $warehouseId)->where('product_id', $product->id)->get();

        $vReturns = 0;
        foreach ($saleReturnItems as $ri) {
            if ($variantMatches($ri->color, $vName)) {
                $vReturns += (float)($ri->qty > 0 ? $ri->qty : (((float)($ri->boxes ?? 0) * $vPpb) + (float)($ri->loose_pieces ?? 0)));
            }
        }

        // 4. Purchases
        $purchaseItems = DB::table('purchase_items')->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereNull('purchases.deleted_at')
            ->where('purchases.warehouse_id', $warehouseId)
            ->where('purchase_items.product_id', $product->id)->get();

        $vPurchases = 0;
        foreach ($purchaseItems as $pi) {
            if ($variantMatches($pi->color, $vName)) {
                $ppbItem = (float)($pi->pieces_per_box > 0 ? $pi->pieces_per_box : $vPpb);
                $pPieces = ((float)$pi->boxes_qty * $ppbItem) + (float)$pi->loose_qty;
                if ($pPieces <= 0) $pPieces = (float)$pi->qty;
                $vPurchases += $pPieces;
            }
        }

        // 5. Transfers Out
        $transfersOut = DB::table('stock_transfers')
            ->where('from_warehouse_id', $warehouseId)->where('product_id', $product->id)->get();
        $vTransfersOutPieces = (float) ($transfersOut->filter(function($t) use ($vKey, $vName) {
            return empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName;
        })->sum('quantity')) * $vPpb;

        // 6. Transfers In
        $transfersIn = DB::table('stock_transfers')
            ->where('to_warehouse_id', $warehouseId)->where('to_shop', false)->where('product_id', $product->id)->get();
        $vTransfersInPieces = (float) ($transfersIn->filter(function($t) use ($vKey, $vName) {
            return empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName;
        })->sum('quantity')) * $vPpb;

        // 7. Adjustments
        $adjustments = DB::table('stock_adjustments')
            ->where('warehouse_id', $warehouseId)->where('product_id', $product->id)->get();
        $vAdj = (float) $adjustments->filter(function($a) use ($vKey, $vName) {
            return empty($a->variant_key) || $a->variant_key === $vKey || $a->variant_name === $vName;
        })->sum(function($a) {
            return (float)$a->new_stock - (float)$a->old_stock;
        });

        return max(0, $opPieces + $vPurchases - $vSales + $vReturns + $vTransfersInPieces - $vTransfersOutPieces + $vAdj);
    }

    /**
     * Private helper to execute a single stock adjustment
     */
    private function processSingleAdjustment(array $data)
    {
        $product = Product::where('id', $data['product_id'])->lockForUpdate()->first();

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

        $variantKey  = $data['variant_key'] ?? null;
        $variantName = $data['variant_name'] ?? null;

        if (empty($variantName) && !empty($product->color)) {
            $parsedVars = is_string($product->color) ? json_decode($product->color, true) : $product->color;
            if (is_array($parsedVars) && count($parsedVars) > 0) {
                $variantName = $parsedVars[0]['name'] ?? $product->item_name;
            }
        }

        $vOldStock = $this->getVariantLivePieces($product, $data['warehouse_id'], $variantKey, $variantName);

        $oldStock = $vOldStock;
        $inputQty = (float) $data['qty'];
        $deltaQty = 0;

        if ($data['type'] === 'add') {
            $deltaQty = $inputQty;
            $newStock = $oldStock + $deltaQty;
        } elseif ($data['type'] === 'subtract') {
            $deltaQty = -1 * min($oldStock, $inputQty);
            $newStock = max(0, $oldStock + $deltaQty);
        } else { // 'set'
            $deltaQty = $inputQty - $oldStock;
            $newStock = max(0, $inputQty);
        }



        // Update WarehouseStock
        $warehouseStock->total_pieces = max(0, (float)$warehouseStock->total_pieces + $deltaQty);
        $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
        if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size'])) {
            $warehouseStock->quantity = floor($warehouseStock->total_pieces / $ppb);
        } else {
            $warehouseStock->quantity = $warehouseStock->total_pieces;
        }
        $warehouseStock->remarks = 'Adjusted via Stock Adjustment module';
        $warehouseStock->save();

        // Log Stock Movement
        DB::table('stock_movements')->insert([
            'product_id'   => $product->id,
            'type'         => 'adjustment',
            'qty'          => $deltaQty,
            'ref_type'     => 'STOCK_ADJUSTMENT',
            'ref_id'       => null,
            'note'         => "Warehouse #{$data['warehouse_id']} | " . ($variantName ? "Variant: {$variantName} | " : '') . "Reason: " . $data['reason'],
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Create StockAdjustment audit record
        StockAdjustment::create([
            'user_id'      => auth()->id(),
            'warehouse_id' => $data['warehouse_id'],
            'product_id'   => $product->id,
            'variant_key'  => $variantKey,
            'variant_name' => $variantName ?: null,
            'type'         => $data['type'],
            'qty'          => $inputQty,
            'old_stock'    => $oldStock,
            'new_stock'    => $newStock,
            'reason'       => $data['reason'],
        ]);
    }
}
