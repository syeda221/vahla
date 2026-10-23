<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\OpeningStock;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpeningStockController extends Controller
{
    /**
     * Check if user is authorized for opening stock operations
     */
    private function checkPermission()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            return true;
        }

        if (
            $user->can('warehouse.stock.view') ||
            $user->can('warehouse.stock.create') ||
            $user->can('warehouse.view') ||
            $user->can('stock.adjust.view') ||
            $user->can('stock.adjust.create')
        ) {
            return true;
        }

        abort(403, 'Unauthorized. You do not have permission to manage Opening Stock.');
    }

    /**
     * Get permitted warehouses
     */
    private function getPermittedWarehouses()
    {
        $user = auth()->user();

        if ($user->email === 'admin@admin.com' || $user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->can('warehouse.view')) {
            return Warehouse::orderBy('warehouse_name')->get();
        }

        if (!empty($user->warehouse_id)) {
            return Warehouse::where('id', $user->warehouse_id)->get();
        }

        $userWarehouses = Warehouse::where('creater_id', $user->id)->get();
        if ($userWarehouses->count() > 0) {
            return $userWarehouses;
        }

        return Warehouse::limit(1)->get();
    }

    /**
     * Format stock quantity according to the project's standard packaging logic:
     * - By cartons: "68 Ctn + 4 Pcs (5 pcs/ctn)" or "75 Ctn (4 pcs/ctn)"
     * - By bandal: "120 Bndl (5 pcs/bndl)" or "X Bndl + Y Pcs (5 pcs/bndl)"
     * - By pieces: "20 Pcs"
     */
    public function formatPackagingQuantity($pieces, $product, $ppb = null)
    {
        $pieces = (float)$pieces;
        $ppb = (float)($ppb > 0 ? $ppb : ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1));
        $sizeMode = $product->size_mode ?? 'by_pieces';
        $isCartonMode = in_array($sizeMode, ['by_cartons', 'by_bandal']) && $ppb > 1;

        if ($isCartonMode) {
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
                'is_carton'     => true,
            ];
        } elseif ($ppb > 1 && $sizeMode === 'by_size') {
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
                'is_carton'     => true,
            ];
        } else {
            $unitName = $product->unit?->name ?? 'Pcs';
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
                'is_carton'     => false,
            ];
        }
    }

    /**
     * Index page: list opening stock entries, stats & creation terminal
     */
    public function index(Request $request)
    {
        $this->checkPermission();

        $permittedWarehouses = $this->getPermittedWarehouses();
        $permittedWarehouseIds = $permittedWarehouses->pluck('id')->toArray();

        // Base query for opening stocks across permitted warehouses
        $baseQuery = OpeningStock::whereIn('warehouse_id', $permittedWarehouseIds);

        if ($request->filled('warehouse_id')) {
            $baseQuery->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('product_id')) {
            $baseQuery->where('product_id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $baseQuery->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('item_name', 'like', "%{$search}%")
                       ->orWhere('item_code', 'like', "%{$search}%");
                })->orWhere('variant_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // Overall stats across permitted warehouses
        $totalEntries    = OpeningStock::whereIn('warehouse_id', $permittedWarehouseIds)->count();
        $totalPieces     = OpeningStock::whereIn('warehouse_id', $permittedWarehouseIds)->sum('total_pieces');
        $totalBoxes      = OpeningStock::whereIn('warehouse_id', $permittedWarehouseIds)
            ->whereHas('product', function ($q) {
                $q->where('size_mode', '!=', 'by_pieces')
                  ->where('pieces_per_box', '>', 1);
            })
            ->sum('boxes_quantity');
        $uniqueProducts  = OpeningStock::whereIn('warehouse_id', $permittedWarehouseIds)->distinct('product_id')->count('product_id');

        // Paginate by (product_id, variant_key, variant_name) so each variant has its OWN separate row
        $productPagination = (clone $baseQuery)
            ->select(
                'product_id',
                DB::raw("COALESCE(variant_key, '') as v_key"),
                DB::raw("COALESCE(variant_name, '') as v_name"),
                DB::raw("MAX(created_at) as latest_created_at")
            )
            ->groupBy('product_id', DB::raw("COALESCE(variant_key, '')"), DB::raw("COALESCE(variant_name, '')"))
            ->orderByDesc('latest_created_at')
            ->paginate(25)
            ->appends($request->query());

        $pageProductIds = $productPagination->pluck('product_id')->unique()->toArray();

        // Get all opening stock entries for these products on the current page
        $allEntries = OpeningStock::with([
                'warehouse.branch',
                'user',
                'product.unit',
                'product.category_relation',
                'product.brand',
            ])
            ->whereIn('warehouse_id', $permittedWarehouseIds)
            ->whereIn('product_id', $pageProductIds)
            ->latest()
            ->get()
            ->groupBy('product_id');

        // Get all live warehouse stocks for these products
        $warehouseStocks = WarehouseStock::with('warehouse')
            ->whereIn('product_id', $pageProductIds)
            ->whereIn('warehouse_id', $permittedWarehouseIds)
            ->get()
            ->groupBy('product_id');

        // Fetch sales, returns, and purchases for these products to calculate live variant stocks accurately
        $saleItems = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.sale_status', '!=', 'cancelled')
            ->whereIn('sale_items.product_id', $pageProductIds)
            ->whereIn('sale_items.warehouse_id', $permittedWarehouseIds)
            ->select('sale_items.product_id', 'sale_items.warehouse_id', 'sale_items.total_pieces', 'sale_items.color')
            ->get();

        $saleReturnItems = DB::table('sale_return_items')
            ->whereIn('product_id', $pageProductIds)
            ->whereIn('warehouse_id', $permittedWarehouseIds)
            ->select('product_id', 'warehouse_id', 'qty', 'boxes', 'loose_pieces', 'color')
            ->get();

        $purchaseItems = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereNull('purchases.deleted_at')
            ->whereIn('purchase_items.product_id', $pageProductIds)
            ->whereIn('purchases.warehouse_id', $permittedWarehouseIds)
            ->select(
                'purchase_items.product_id',
                'purchases.warehouse_id',
                'purchase_items.qty',
                'purchase_items.boxes_qty',
                'purchase_items.loose_qty',
                'purchase_items.pieces_per_box',
                'purchase_items.color'
            )
            ->get();

        // Fetch stock transfers for accurate live stock calculation
        $stockTransfersOut = DB::table('stock_transfers')
            ->whereIn('product_id', $pageProductIds)
            ->whereIn('from_warehouse_id', $permittedWarehouseIds)
            ->select('product_id', 'from_warehouse_id as warehouse_id', 'quantity', 'variant_key', 'variant_name')
            ->get();

        $stockTransfersIn = DB::table('stock_transfers')
            ->whereIn('product_id', $pageProductIds)
            ->whereIn('to_warehouse_id', $permittedWarehouseIds)
            ->where('to_shop', false)
            ->select('product_id', 'to_warehouse_id as warehouse_id', 'quantity', 'variant_key', 'variant_name')
            ->get();

        $variantMatches = function ($rawColor, $targetVariantName, $targetVariantKey) {
            if (!$targetVariantName && !$targetVariantKey) {
                return true;
            }
            if (empty($rawColor)) {
                return false;
            }
            $raw = $rawColor;
            $b64 = base64_decode($raw, true);
            if ($b64) $raw = $b64;
            $arr = json_decode($raw, true);
            if (!is_array($arr)) {
                return false;
            }
            $name = $arr['name'] ?? null;
            if ($targetVariantName && $name === $targetVariantName) {
                return true;
            }
            if ($targetVariantKey) {
                $k = ($arr['name'] ?? '') . '|' . ($arr['size'] ?? '-') . '|' . ($arr['color'] ?? '-');
                if ($k === $targetVariantKey) {
                    return true;
                }
            }
            return false;
        };

        $groupedProducts = collect();
        $productVariantsCache = [];

        foreach ($productPagination as $pItem) {
            $pId   = $pItem->product_id;
            $vKey  = (string)$pItem->v_key;
            $vName = (string)$pItem->v_name;

            $productEntries = $allEntries->get($pId, collect());
            if ($productEntries->isEmpty()) {
                continue;
            }

            // Filter for this specific variant
            $entries = $productEntries->filter(function ($e) use ($vKey, $vName) {
                $eKey = (string)($e->variant_key ?? '');
                $eName = (string)($e->variant_name ?? '');
                if ($vKey !== '') {
                    return $eKey === $vKey || ($vName !== '' && $eName === $vName);
                }
                if ($vName !== '') {
                    return $eName === $vName;
                }
                return $eKey === '' && $eName === '';
            });

            if ($entries->isEmpty()) {
                continue;
            }

            $firstEntry = $entries->first();
            $product = $firstEntry->product;
            if (!$product) {
                continue;
            }

            $ppb = (float)($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
            $isPieceOnly = ($product->size_mode === 'by_pieces' || $ppb <= 1);
            $pWarehouseStocks = $warehouseStocks->get($pId, collect())->keyBy('warehouse_id');

            // Opening quantities for this variant
            $totalOpeningPieces = (float)$entries->sum('total_pieces');
            $totalOpeningBoxes  = $isPieceOnly ? 0 : (float)$entries->sum('boxes_quantity');

            // Cache variants for this product
            if (!isset($productVariantsCache[$pId])) {
                $productVariantsCache[$pId] = $this->extractProductVariants($product);
            }

            // Match variant in product variants cache if it has variant
            $matchedVariant = null;
            if ($vKey !== '' || $vName !== '') {
                foreach ($productVariantsCache[$pId] as $v) {
                    if (($vKey !== '' && ($v['variant_key'] ?? '') === $vKey) ||
                        ($vName !== '' && ($v['name'] ?? '') === $vName)) {
                        $matchedVariant = $v;
                        break;
                    }
                }
            }
            if ($matchedVariant && !empty($matchedVariant['conv_factor']) && (float)$matchedVariant['conv_factor'] > 0) {
                $ppb = (float)$matchedVariant['conv_factor'];
                $isPieceOnly = ($product->size_mode === 'by_pieces' || $ppb <= 1);
            }

            // Resolve sale price for this variant
            $salePrice = 0;
            foreach ($entries as $e) {
                if (!empty($e->sale_price) && (float)$e->sale_price > 0) {
                    $salePrice = (float)$e->sale_price;
                    break;
                }
            }
            if ($salePrice <= 0 && $matchedVariant && !empty($matchedVariant['sale_price']) && (float)$matchedVariant['sale_price'] > 0) {
                $salePrice = (float)$matchedVariant['sale_price'];
            }
            if ($salePrice <= 0) {
                $salePrice = (float)($product->sale_price_per_piece ?? 0);
            }

            // Resolve cost price for this variant
            $costPrice = 0;
            foreach ($entries as $e) {
                if (!empty($e->cost_price) && (float)$e->cost_price > 0) {
                    $costPrice = (float)$e->cost_price;
                    break;
                }
            }
            if ($costPrice <= 0 && $matchedVariant && !empty($matchedVariant['cost_price']) && (float)$matchedVariant['cost_price'] > 0) {
                $costPrice = (float)$matchedVariant['cost_price'];
            }
            if ($costPrice <= 0) {
                $costPrice = (float)($product->purchase_price_per_piece ?? 0);
            }

            $totalValue = $totalOpeningPieces * $costPrice;

            // Warehouses breakdown & accurate live stock calculation for THIS variant
            $warehouseList = [];
            $totalLivePieces = 0;

            // Identify all warehouses that have opening entries OR live activity (returns, purchases) for this variant
            $entryWhIds = $entries->pluck('warehouse_id')->toArray();
            $allVariantWhIds = collect($entryWhIds);
            foreach ($permittedWarehouseIds as $pWhId) {
                if (!in_array($pWhId, $entryWhIds)) {
                    $hasReturns = $saleReturnItems->where('product_id', $pId)->where('warehouse_id', $pWhId)->contains(function($ri) use ($variantMatches, $vName, $vKey) {
                        return $variantMatches($ri->color, $vName, $vKey);
                    });
                    $hasPurchases = $purchaseItems->where('product_id', $pId)->where('warehouse_id', $pWhId)->contains(function($pi) use ($variantMatches, $vName, $vKey) {
                        return $variantMatches($pi->color, $vName, $vKey);
                    });
                    $hasTransfersIn = $stockTransfersIn->where('product_id', $pId)->where('warehouse_id', $pWhId)->contains(function($st) use ($vName, $vKey) {
                        return empty($st->variant_key) || $st->variant_key === $vKey || $st->variant_name === $vName;
                    });
                    $hasTransfersOut = $stockTransfersOut->where('product_id', $pId)->where('warehouse_id', $pWhId)->contains(function($st) use ($vName, $vKey) {
                        return empty($st->variant_key) || $st->variant_key === $vKey || $st->variant_name === $vName;
                    });
                    if ($hasReturns || $hasPurchases || $hasTransfersIn || $hasTransfersOut) {
                        $allVariantWhIds->push($pWhId);
                    }
                }
            }
            $allVariantWhIds = $allVariantWhIds->unique()->values();

            foreach ($allVariantWhIds as $whId) {
                $entry = $entries->firstWhere('warehouse_id', $whId);
                $wh = $entry ? $entry->warehouse : $permittedWarehouses->firstWhere('id', $whId);
                $whName = $wh?->warehouse_name ?? 'N/A';
                $whLocation = $wh?->location ?? '';

                $entryOpeningPieces = $entry ? (float)$entry->total_pieces : 0;
                $entryOpeningBoxes  = ($isPieceOnly || !$entry) ? 0 : (float)$entry->boxes_quantity;

                // Calculate accurate warehouse-specific live pieces considering sales, returns, purchases
                $whSales = 0;
                foreach ($saleItems->where('product_id', $pId)->where('warehouse_id', $whId) as $si) {
                    if ($variantMatches($si->color, $vName, $vKey)) {
                        $whSales += (float)$si->total_pieces;
                    }
                }

                $whReturns = 0;
                foreach ($saleReturnItems->where('product_id', $pId)->where('warehouse_id', $whId) as $ri) {
                    if ($variantMatches($ri->color, $vName, $vKey)) {
                        $rPieces = (float)($ri->qty > 0 ? $ri->qty : (((float)$ri->boxes * $ppb) + (float)$ri->loose_pieces));
                        $whReturns += $rPieces;
                    }
                }

                $whPurchases = 0;
                foreach ($purchaseItems->where('product_id', $pId)->where('warehouse_id', $whId) as $pi) {
                    if ($variantMatches($pi->color, $vName, $vKey)) {
                        $ppbItem = (float)($pi->pieces_per_box > 0 ? $pi->pieces_per_box : $ppb);
                        $pPieces = ((float)$pi->boxes_qty * $ppbItem) + (float)$pi->loose_qty;
                        if ($pPieces <= 0) $pPieces = (float)$pi->qty;
                        $whPurchases += $pPieces;
                    }
                }

                // Transfer adjustments for this variant & warehouse
                $whTransferOut = 0;
                $whTransferIn  = 0;
                foreach ($stockTransfersOut->where('product_id', $pId)->where('warehouse_id', $whId) as $st) {
                    if (empty($st->variant_key) || $st->variant_key === $vKey || $st->variant_name === $vName) {
                        $whTransferOut += ((float)$st->quantity * $ppb);
                    }
                }
                foreach ($stockTransfersIn->where('product_id', $pId)->where('warehouse_id', $whId) as $st) {
                    if (empty($st->variant_key) || $st->variant_key === $vKey || $st->variant_name === $vName) {
                        $whTransferIn += ((float)$st->quantity * $ppb);
                    }
                }

                $entryLivePieces = max(0, $entryOpeningPieces + $whPurchases - $whSales + $whReturns + $whTransferIn - $whTransferOut);
                if ($entryLivePieces <= 0 && $entryOpeningPieces <= 0) {
                    continue;
                }

                $entryLiveBoxes  = (!$isPieceOnly && $ppb > 1) ? round($entryLivePieces / $ppb, 2) : 0;
                $totalLivePieces += $entryLivePieces;

                $warehouseList[] = [
                    'entry_id'       => $entry?->id,
                    'warehouse_id'   => $whId,
                    'warehouse_name' => $whName,
                    'location'       => $whLocation,
                    'variant_name'   => $vName,
                    'variant_key'    => $vKey,
                    'opening_pieces'    => $entryOpeningPieces,
                    'opening_boxes'     => $entryOpeningBoxes,
                    'opening_formatted' => $this->formatPackagingQuantity($entryOpeningPieces, $product, $ppb),
                    'live_pieces'       => $entryLivePieces,
                    'live_boxes'        => $entryLiveBoxes,
                    'live_formatted'    => $this->formatPackagingQuantity($entryLivePieces, $product, $ppb),
                    'cost_price'        => (float)($entry?->cost_price ?? $costPrice),
                    'sale_price'        => (float)($entry?->sale_price ?? $salePrice),
                    'date'              => $entry?->created_at ? $entry->created_at->format('d M, Y h:i A') : '-',
                    'added_by'          => $entry?->user?->name ?? 'System',
                ];
            }

            $totalLiveBoxes = 0;
            if (!$isPieceOnly && $ppb > 1 && $totalLivePieces > 0) {
                $totalLiveBoxes = round($totalLivePieces / $ppb, 2);
            }

            $rowKey = $product->id . '_' . ($vKey !== '' ? md5($vKey) : ($vName !== '' ? md5($vName) : 'base'));

            $groupedProducts->push((object)[
                'row_key'              => $rowKey,
                'product_id'           => $product->id,
                'product'              => $product,
                'product_name'         => $product->item_name,
                'product_code'         => $product->item_code,
                'category_name'        => $product->category_relation?->name ?? '',
                'brand_name'           => $product->brand?->name ?? '',
                'product_image'        => $product->image ? asset('uploads/products/' . $product->image) : null,
                'unit_name'            => $product->unit?->name ?? 'Pieces',
                'ppb'                  => $ppb,
                'size_mode'            => $product->size_mode,
                'is_piece_only'        => $isPieceOnly,
                'has_boxes'            => !$isPieceOnly,
                'variant_key'          => $vKey,
                'variant_name'         => $vName,
                'variant_display'      => $vName !== '' ? $vName : '-',
                'latest_created_at'    => $pItem->latest_created_at ? \Carbon\Carbon::parse($pItem->latest_created_at) : $firstEntry->created_at,
                'total_opening_pieces' => $totalOpeningPieces,
                'total_opening_boxes'  => $totalOpeningBoxes,
                'opening_formatted'    => $this->formatPackagingQuantity($totalOpeningPieces, $product, $ppb),
                'total_live_pieces'    => $totalLivePieces,
                'total_live_boxes'     => $totalLiveBoxes,
                'live_formatted'       => $this->formatPackagingQuantity($totalLivePieces, $product, $ppb),
                'cost_price'           => $costPrice,
                'sale_price'           => $salePrice,
                'total_value'          => $totalValue,
                'warehouses_count'     => count($warehouseList),
                'first_warehouse_name' => $warehouseList[0]['warehouse_name'] ?? 'N/A',
                'warehouses'           => $warehouseList,
                'all_entry_ids'        => $entries->pluck('id')->toArray(),
            ]);
        }

        // Products list for dropdowns with parsed variants & prices
        $products = Product::with('unit')
            ->select('id', 'item_name', 'item_code', 'size_mode', 'pieces_per_box', 'purchase_price_per_piece', 'wholesale_price', 'sale_price_per_piece', 'alert_quantity', 'alert_carton_quantity', 'color', 'image', 'unit_id')
            ->orderBy('item_name')
            ->get()
            ->map(function ($p) {
                $variants = $this->extractProductVariants($p);
                $p->has_variants   = count($variants) > 0;
                $p->variants       = $variants;
                $p->cost_price     = (float)($p->purchase_price_per_piece ?? 0);
                $p->wholesale_price = (float)($p->wholesale_price ?? $p->purchase_price_per_piece ?? 0);
                $p->sale_price     = (float)($p->sale_price_per_piece ?? 0);
                $p->alert_qty      = (float)($p->alert_quantity ?? 0);
                $p->unit_name      = $p->unit?->name ?? 'Pcs';
                return $p;
            });

        return view('admin_panel.inventory.opening_stock.index', [
            'groupedProducts'     => $groupedProducts,
            'productPagination'   => $productPagination,
            'totalEntries'        => $totalEntries,
            'totalPieces'         => $totalPieces,
            'totalBoxes'          => $totalBoxes,
            'uniqueProducts'      => $uniqueProducts,
            'warehouses'          => $permittedWarehouses,
            'products'            => $products,
        ]);
    }

    /**
     * Dedicated Create Opening Stock Page
     */
    public function create()
    {
        $this->checkPermission();

        $permittedWarehouses = $this->getPermittedWarehouses();

        $products = Product::with('unit')
            ->select('id', 'item_name', 'item_code', 'size_mode', 'pieces_per_box', 'purchase_price_per_piece', 'wholesale_price', 'sale_price_per_piece', 'alert_quantity', 'alert_carton_quantity', 'color', 'image', 'unit_id')
            ->orderBy('item_name')
            ->get()
            ->map(function ($p) {
                $variants = $this->extractProductVariants($p);
                $p->has_variants    = count($variants) > 0;
                $p->variants        = $variants;
                $p->cost_price      = (float)($p->purchase_price_per_piece ?? 0);
                $p->wholesale_price = (float)($p->wholesale_price ?? $p->purchase_price_per_piece ?? 0);
                $p->sale_price      = (float)($p->sale_price_per_piece ?? 0);
                $p->alert_qty       = (float)($p->alert_quantity ?? 0);
                $p->unit_name       = $p->unit?->name ?? 'Pcs';
                return $p;
            });

        return view('admin_panel.inventory.opening_stock.create', [
            'warehouses' => $permittedWarehouses,
            'products'   => $products,
        ]);
    }

    /**
     * Helper to extract parsed variants with cost price and formatted labels
     */
    public function getVariantConversionFactor(Product $product, ?string $variantKey = null, ?string $variantName = null): float
    {
        $ppb = (float)($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
        if (!empty($product->color) && ($variantKey || $variantName)) {
            $variants = $this->extractProductVariants($product);
            foreach ($variants as $v) {
                if (($variantKey && ($v['variant_key'] ?? '') === $variantKey) ||
                    ($variantName && ($v['name'] ?? '') === $variantName)) {
                    if (!empty($v['conv_factor']) && (float)$v['conv_factor'] > 0) {
                        return (float)$v['conv_factor'];
                    }
                    break;
                }
            }
        }
        return $ppb;
    }

    public function extractProductVariants($product): array
    {
        $variants = [];
        if (empty($product->color)) {
            return $variants;
        }

        try {
            $raw = $product->color;
            if (is_string($raw)) {
                $b64 = base64_decode($raw, true);
                if ($b64 !== false && (str_starts_with(trim($b64), '[') || str_starts_with(trim($b64), '{'))) {
                    $raw = $b64;
                }
                $decoded = json_decode($raw, true);
                if (is_string($decoded)) {
                    $decoded2 = json_decode($decoded, true);
                    if (is_array($decoded2)) {
                        $decoded = $decoded2;
                    }
                }
            } else {
                $decoded = $raw;
            }

            if (is_array($decoded)) {
                if (isset($decoded['name'])) {
                    $decoded = [$decoded];
                } elseif (isset($decoded[0]) && is_string($decoded[0])) {
                    $newDecoded = [];
                    foreach ($decoded as $cName) {
                        $newDecoded[] = [
                            'name'        => $product->item_name . ($cName ? ' - ' . $cName : ''),
                            'size'        => '',
                            'color'       => $cName,
                            'purch_price' => $product->purchase_price_per_piece ?? 0,
                        ];
                    }
                    $decoded = $newDecoded;
                }

                foreach ($decoded as $v) {
                    if (!is_array($v)) continue;
                    $vName  = $v['name'] ?? $product->item_name;
                    $vSize  = $v['size'] ?? '-';
                    $vColor = $v['color'] ?? '-';
                    $key    = $vName . '|' . $vSize . '|' . $vColor;

                    $vCost = 0;
                    if (isset($v['purch_price']) && is_numeric($v['purch_price']) && (float)$v['purch_price'] > 0) {
                        $vCost = (float)$v['purch_price'];
                    } elseif (!empty($product->purchase_price_per_piece) && is_numeric($product->purchase_price_per_piece)) {
                        $vCost = (float)$product->purchase_price_per_piece;
                    }

                    $vSale = 0;
                    if (isset($v['sale_price']) && is_numeric($v['sale_price']) && (float)$v['sale_price'] > 0) {
                        $vSale = (float)$v['sale_price'];
                    } elseif (!empty($product->sale_price_per_piece) && is_numeric($product->sale_price_per_piece)) {
                        $vSale = (float)$product->sale_price_per_piece;
                    }

                    $vAlert = 0;
                    if (isset($v['alert']) && is_numeric($v['alert'])) {
                        $vAlert = (float)$v['alert'];
                    } elseif (!empty($product->alert_quantity) && is_numeric($product->alert_quantity)) {
                        $vAlert = (float)$product->alert_quantity;
                    }

                    $label = $vName;
                    $details = [];
                    if ($vSize && $vSize !== '-') $details[] = "Size: {$vSize}";
                    if ($vColor && $vColor !== '-') $details[] = "Color: {$vColor}";
                    if (!empty($details)) {
                        $label .= ' (' . implode(', ', $details) . ')';
                    }
                    if ($vCost > 0) {
                        $label .= ' — Cost: Rs. ' . number_format($vCost, 2);
                    }
                    if ($vSale > 0) {
                        $label .= ' | Sale: Rs. ' . number_format($vSale, 2);
                    }

                    $variants[] = [
                        'variant_key'    => $key,
                        'name'           => $vName,
                        'size'           => $vSize,
                        'color'          => $vColor,
                        'cost_price'     => $vCost,
                        'sale_price'     => $vSale,
                        'alert_qty'      => $vAlert,
                        'conv_factor'    => (float)($v['conv_factor'] ?? 1),
                        'stock'          => (float)($v['stock'] ?? 0),
                        'display_label'  => $label,
                    ];
                }
            }
        } catch (\Exception $e) {}

        return $variants;
    }

    /**
     * AJAX: Search products by name, code or barcode
     */
    public function searchProducts(Request $request)
    {
        $this->checkPermission();

        $term = trim($request->get('q', ''));

        $products = Product::query()
            ->with('unit')
            ->select('id', 'item_name', 'item_code', 'pieces_per_box', 'size_mode', 'purchase_price_per_piece', 'wholesale_price', 'sale_price_per_piece', 'alert_quantity', 'alert_carton_quantity', 'color', 'image', 'unit_id')
            ->when(!empty($term), function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('item_name', 'like', "%{$term}%")
                      ->orWhere('item_code', 'like', "%{$term}%")
                      ->orWhere('barcode_path', 'like', "%{$term}%");
                });
            })
            ->orderBy('item_name')
            ->limit(100)
            ->get();

        return response()->json($products->map(function ($p) {
            $variants = $this->extractProductVariants($p);
            return [
                'id'              => $p->id,
                'text'            => "{$p->item_code} - {$p->item_name}",
                'item_name'       => $p->item_name,
                'item_code'       => $p->item_code,
                'pieces_per_box'  => (int)($p->pieces_per_box ?? 1),
                'size_mode'       => $p->size_mode,
                'cost_price'      => (float)($p->purchase_price_per_piece ?? 0),
                'wholesale_price' => (float)($p->wholesale_price ?? $p->purchase_price_per_piece ?? 0),
                'sale_price'      => (float)($p->sale_price_per_piece ?? 0),
                'alert_quantity'  => (float)($p->alert_quantity ?? 0),
                'unit_name'       => $p->unit?->name ?? 'Pcs',
                'image_url'       => $p->image ? asset('uploads/products/' . $p->image) : null,
                'has_variants'    => count($variants) > 0,
                'variants'        => $variants,
            ];
        }));
    }

    /**
     * AJAX: Get current stock for a product across all warehouses & variant list
     */
    public function getProductStock(Request $request, $productId)
    {
        $this->checkPermission();

        $product = Product::with(['unit'])->findOrFail($productId);
        $warehouses = $this->getPermittedWarehouses();

        // Warehouse stocks lookup
        $existingStocks = WarehouseStock::where('product_id', $productId)
            ->whereIn('warehouse_id', $warehouses->pluck('id'))
            ->get()
            ->keyBy('warehouse_id');

        $warehouseList = [];
        foreach ($warehouses as $wh) {
            $stock = $existingStocks->get($wh->id);
            $totalPcs = $stock ? (float)$stock->total_pieces : 0;
            $boxes = $stock ? (float)$stock->quantity : 0;

            $warehouseList[] = [
                'warehouse_id'   => $wh->id,
                'warehouse_name' => $wh->warehouse_name,
                'location'       => $wh->location ?? '',
                'current_pieces' => $totalPcs,
                'current_boxes'  => $boxes,
            ];
        }

        // Parse variants with cost price
        $variants = $this->extractProductVariants($product);

        return response()->json([
            'success'            => true,
            'product'            => [
                'id'             => $product->id,
                'item_name'      => $product->item_name,
                'item_code'      => $product->item_code,
                'size_mode'      => $product->size_mode,
                'pieces_per_box' => (int)($product->pieces_per_box ?? 1),
                'cost_price'     => (float)($product->purchase_price_per_piece ?? 0),
                'sale_price'     => (float)($product->sale_price_per_piece ?? 0),
                'alert_quantity' => (float)($product->alert_quantity ?? 0),
                'unit_name'      => $product->unit->name ?? 'Pcs',
                'image_url'      => $product->image ? asset('uploads/products/' . $product->image) : null,
            ],
            'warehouses'         => $warehouseList,
            'has_variants'       => count($variants) > 0,
            'variants'           => $variants,
        ]);
    }

    /**
     * Store Opening Stock (supports both Matrix & Batch multi-row format)
     */
    public function store(Request $request)
    {
        $this->checkPermission();

        $entryType = $request->input('entry_type', 'matrix');

        try {
            DB::beginTransaction();

            $createdCount = 0;
            $totalPiecesAdded = 0;

            if ($entryType === 'unified') {
                $productsInput = $request->input('products', []);
                if (empty($productsInput) || !is_array($productsInput)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No products provided. Please enter at least one product with quantity.'
                    ], 422);
                }

                foreach ($productsInput as $pData) {
                    $pId = (int)($pData['product_id'] ?? 0);
                    if (!$pId) continue;

                    $product = Product::lockForUpdate()->findOrFail($pId);
                    $variantKey  = !empty($pData['variant_key']) ? trim($pData['variant_key']) : null;
                    $variantName = !empty($pData['variant_name']) ? trim($pData['variant_name']) : null;
                    if ($variantKey && !$variantName) {
                        $parts = explode('|', $variantKey);
                        $variantName = $parts[0] ?? null;
                    }
                    $ppb = $this->getVariantConversionFactor($product, $variantKey, $variantName);

                    $wholesalePrice = (isset($pData['wholesale_price']) && is_numeric($pData['wholesale_price'])) ? (float)$pData['wholesale_price'] : (float)($product->wholesale_price ?? $product->purchase_price_per_piece ?? 0);
                    $costPrice = (isset($pData['cost_price']) && is_numeric($pData['cost_price'])) ? (float)$pData['cost_price'] : $wholesalePrice;
                    $salePrice = (isset($pData['retail_price']) && is_numeric($pData['retail_price'])) ? (float)$pData['retail_price'] : (float)($product->sale_price_per_piece ?? 0);
                    $alertQty = (isset($pData['alert_qty']) && is_numeric($pData['alert_qty'])) ? (float)$pData['alert_qty'] : (float)($product->alert_quantity ?? 0);

                    $locations = $pData['locations'] ?? [];
                    if (!is_array($locations)) continue;

                    foreach ($locations as $loc) {
                        $whId = (int)($loc['warehouse_id'] ?? 0);
                        $qty  = (float)($loc['qty'] ?? 0);
                        $unit = strtolower(trim($loc['unit'] ?? 'pcs'));
                        $note = !empty($loc['notes']) ? trim($loc['notes']) : 'Opening Stock';

                        if ($whId <= 0 || $qty <= 0) {
                            continue;
                        }

                        $calculatedTotalPieces = 0;
                        $boxesCount = 0;

                        if ($unit === 'carton' || $unit === 'bandal' || $unit === 'box') {
                            $boxesCount = $qty;
                            $calculatedTotalPieces = $qty * $ppb;
                        } else {
                            $calculatedTotalPieces = $qty;
                            $boxesCount = ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) ? round($qty / $ppb, 2) : 0;
                        }

                        if ($calculatedTotalPieces <= 0) {
                            continue;
                        }

                        $this->saveOpeningStockEntry(
                            $whId,
                            $product,
                            $variantKey,
                            $variantName,
                            $boxesCount,
                            $calculatedTotalPieces,
                            $costPrice,
                            $salePrice,
                            $alertQty,
                            $note
                        );

                        if ($wholesalePrice > 0 && !$variantKey) {
                            $product->wholesale_price = $wholesalePrice;
                            $product->save();
                        }

                        $createdCount++;
                        $totalPiecesAdded += $calculatedTotalPieces;
                    }
                }

            } elseif ($entryType === 'matrix') {
                // Multi-Warehouse for a single product
                $request->validate([
                    'product_id'     => 'required|exists:products,id',
                    'stocks'         => 'required|array|min:1',
                    'stocks.*.warehouse_id' => 'required|exists:warehouses,id',
                ]);

                $product = Product::lockForUpdate()->findOrFail($request->product_id);
                $variantKey  = $request->variant_key ? trim($request->variant_key) : null;
                $variantName = $request->variant_name ? trim($request->variant_name) : null;
                if ($variantKey && !$variantName) {
                    $parts = explode('|', $variantKey);
                    $variantName = $parts[0] ?? null;
                }
                $ppb = $this->getVariantConversionFactor($product, $variantKey, $variantName);
                $costPrice   = (isset($request->cost_price) && is_numeric($request->cost_price)) ? (float)$request->cost_price : (float)($product->purchase_price_per_piece ?? 0);
                $salePrice   = (isset($request->sale_price) && is_numeric($request->sale_price)) ? (float)$request->sale_price : (float)($product->sale_price_per_piece ?? 0);
                $globalNote  = 'Opening Stock';

                foreach ($request->stocks as $stockData) {
                    $whId = (int)$stockData['warehouse_id'];
                    $boxes = isset($stockData['boxes']) ? (float)$stockData['boxes'] : 0;
                    $pieces = isset($stockData['pieces']) ? (float)$stockData['pieces'] : 0;

                    // Calculate total pieces
                    $calculatedTotalPieces = 0;
                    if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) {
                        $calculatedTotalPieces = ($boxes * $ppb) + $pieces;
                        $boxesCount = $boxes + ($ppb > 0 ? floor($pieces / $ppb) : 0);
                    } else {
                        // For pieces or single unit modes
                        $calculatedTotalPieces = $pieces > 0 ? $pieces : $boxes;
                        $boxesCount = 0;
                    }

                    if ($calculatedTotalPieces <= 0) {
                        continue; // Skip warehouse where no stock was entered
                    }

                    $alertQty = (isset($stockData['alert_qty']) && is_numeric($stockData['alert_qty'])) ? (float)$stockData['alert_qty'] : (float)($product->alert_quantity ?? 0);
                    $note = !empty($stockData['notes']) ? $stockData['notes'] : $globalNote;

                    $this->saveOpeningStockEntry(
                        $whId,
                        $product,
                        $variantKey,
                        $variantName,
                        $boxesCount,
                        $calculatedTotalPieces,
                        $costPrice,
                        $salePrice,
                        $alertQty,
                        $note
                    );

                    $createdCount++;
                    $totalPiecesAdded += $calculatedTotalPieces;
                }

            } else {
                // Batch multi-item rows
                $request->validate([
                    'items'                     => 'required|array|min:1',
                    'items.*.product_id'        => 'required|exists:products,id',
                    'items.*.warehouse_id'      => 'required|exists:warehouses,id',
                ]);

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $variantKey  = !empty($item['variant_key']) ? trim($item['variant_key']) : null;
                    $variantName = !empty($item['variant_name']) ? trim($item['variant_name']) : null;
                    $ppb = $this->getVariantConversionFactor($product, $variantKey, $variantName);
                    $whId = (int)$item['warehouse_id'];
                    $boxes = isset($item['boxes']) ? (float)$item['boxes'] : 0;
                    $pieces = isset($item['pieces']) ? (float)$item['pieces'] : 0;

                    $calculatedTotalPieces = 0;
                    if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) {
                        $calculatedTotalPieces = ($boxes * $ppb) + $pieces;
                        $boxesCount = $boxes + ($ppb > 0 ? floor($pieces / $ppb) : 0);
                    } else {
                        $calculatedTotalPieces = $pieces > 0 ? $pieces : $boxes;
                        $boxesCount = 0;
                    }

                    if ($calculatedTotalPieces <= 0) {
                        continue;
                    }

                    $variantKey  = !empty($item['variant_key']) ? trim($item['variant_key']) : null;
                    $variantName = !empty($item['variant_name']) ? trim($item['variant_name']) : null;
                    if ($variantKey && !$variantName) {
                        $parts = explode('|', $variantKey);
                        $variantName = $parts[0] ?? null;
                    }
                    $costPrice   = (isset($item['cost_price']) && is_numeric($item['cost_price'])) ? (float)$item['cost_price'] : (float)($product->purchase_price_per_piece ?? 0);
                    $salePrice   = (isset($item['sale_price']) && is_numeric($item['sale_price'])) ? (float)$item['sale_price'] : (float)($product->sale_price_per_piece ?? 0);
                    $alertQty    = (isset($item['alert_qty']) && is_numeric($item['alert_qty'])) ? (float)$item['alert_qty'] : (float)($product->alert_quantity ?? 0);
                    $note        = !empty($item['notes']) ? $item['notes'] : 'Opening Stock';

                    $this->saveOpeningStockEntry(
                        $whId,
                        $product,
                        $variantKey,
                        $variantName,
                        $boxesCount,
                        $calculatedTotalPieces,
                        $costPrice,
                        $salePrice,
                        $alertQty,
                        $note
                    );

                    $createdCount++;
                    $totalPiecesAdded += $calculatedTotalPieces;
                }
            }

            if ($createdCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No valid quantities entered. Please enter at least 1 box or piece for at least one warehouse.'
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully recorded Opening Stock across {$createdCount} warehouse entry(ies) ({$totalPiecesAdded} total pieces)!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save Opening Stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to process a single opening stock entry
     */
    private function saveOpeningStockEntry(
        int $warehouseId,
        Product $product,
        ?string $variantKey,
        ?string $variantName,
        float $boxes,
        float $totalPieces,
        float $costPrice,
        float $salePrice,
        float $alertQty,
        string $notes
    ) {
        $warehouse = Warehouse::find($warehouseId);
        $whName = $warehouse ? $warehouse->warehouse_name : "#{$warehouseId}";

        // 1. Create OpeningStock record
        $openingStock = OpeningStock::create([
            'user_id'        => auth()->id(),
            'warehouse_id'   => $warehouseId,
            'product_id'     => $product->id,
            'variant_key'    => $variantKey,
            'variant_name'   => $variantName,
            'boxes_quantity' => $boxes,
            'total_pieces'   => $totalPieces,
            'cost_price'     => $costPrice,
            'sale_price'     => $salePrice,
            'alert_quantity' => $alertQty,
            'notes'          => $notes,
        ]);

        // 2. Update or Create WarehouseStock
        $ws = WarehouseStock::firstOrCreate(
            [
                'warehouse_id' => $warehouseId,
                'product_id'   => $product->id,
            ],
            [
                'quantity'     => 0,
                'total_pieces' => 0,
                'remarks'      => 'Opening Stock',
            ]
        );

        $oldStock = (float)$ws->total_pieces;
        $newStock = $oldStock + $totalPieces;
        $ws->total_pieces = $newStock;

        $ppb = (float)($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
        if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) {
            $ws->quantity = round($newStock / $ppb, 2);
        } else {
            $ws->quantity = $newStock;
        }
        $ws->remarks = 'Opening Stock added';
        $ws->save();

        // 3. Update variant stock & prices in product JSON if applicable
        $productUpdated = false;
        if ($variantKey && !empty($product->color)) {
            try {
                $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                if (is_array($parsed)) {
                    foreach ($parsed as &$v) {
                        $vKey = ($v['name'] ?? $product->item_name) . '|' . ($v['size'] ?? '-') . '|' . ($v['color'] ?? '-');
                        if ($vKey === $variantKey || ($variantName && ($v['name'] ?? '') === $variantName)) {
                            $v['stock'] = (float)($v['stock'] ?? 0) + $totalPieces;
                            if ($salePrice > 0) {
                                $v['sale_price'] = $salePrice;
                            }
                            if ($costPrice > 0) {
                                $v['purch_price'] = $costPrice;
                            }
                            if ($alertQty > 0) {
                                $v['alert'] = (string)$alertQty;
                            }
                            break;
                        }
                    }
                    unset($v);
                    $product->color = json_encode($parsed);
                    $productUpdated = true;
                }
            } catch (\Exception $e) {}
        }

        // If no variant or base product prices/alert updated
        if (!$variantKey) {
            if ($salePrice > 0) {
                $product->sale_price_per_piece = $salePrice;
                $productUpdated = true;
            }
            if ($costPrice > 0) {
                $product->purchase_price_per_piece = $costPrice;
                $productUpdated = true;
            }
            if ($alertQty > 0) {
                $product->alert_quantity = $alertQty;
                $productUpdated = true;
            }
        }

        if ($productUpdated) {
            $product->save();
        }

        // 4. Log Stock Movement
        $extraInfo = [];
        if ($salePrice > 0) $extraInfo[] = "Sale: Rs. {$salePrice}";
        if ($alertQty > 0) $extraInfo[] = "Alert: {$alertQty}";
        $extraStr = !empty($extraInfo) ? ' | ' . implode(' | ', $extraInfo) : '';

        StockMovement::create([
            'product_id' => $product->id,
            'type'       => 'adjustment',
            'qty'        => $totalPieces,
            'ref_type'   => 'OPENING_STOCK',
            'ref_id'     => $openingStock->id,
            'note'       => "Opening Stock | Warehouse: {$whName}" . ($variantName ? " | Variant: {$variantName}" : '') . $extraStr,
        ]);

        return $openingStock;
    }

    /**
     * Update Product Opening Stock across warehouses
     */
    public function updateProductOpeningStock(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'product_id'            => 'required|exists:products,id',
            'stocks'                => 'required|array|min:1',
            'stocks.*.warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            DB::beginTransaction();

            $productId   = (int)$request->product_id;
            $variantKey  = $request->filled('variant_key') ? trim($request->variant_key) : null;
            $variantName = $request->filled('variant_name') ? trim($request->variant_name) : null;
            $product     = Product::lockForUpdate()->findOrFail($productId);
            $ppb         = $this->getVariantConversionFactor($product, $variantKey, $variantName);

            $costPrice = (isset($request->cost_price) && is_numeric($request->cost_price)) ? (float)$request->cost_price : (float)($product->purchase_price_per_piece ?? 0);
            $salePrice = (isset($request->sale_price) && is_numeric($request->sale_price)) ? (float)$request->sale_price : (float)($product->sale_price_per_piece ?? 0);

            // Update product master prices or variant prices
            if ($variantKey && !empty($product->color)) {
                try {
                    $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                    if (is_array($parsed)) {
                        foreach ($parsed as &$v) {
                            $vKey = ($v['name'] ?? $product->item_name) . '|' . ($v['size'] ?? '-') . '|' . ($v['color'] ?? '-');
                            if ($vKey === $variantKey || ($variantName && ($v['name'] ?? '') === $variantName)) {
                                if ($salePrice > 0) $v['sale_price'] = $salePrice;
                                if ($costPrice > 0) $v['purch_price'] = $costPrice;
                                break;
                            }
                        }
                        unset($v);
                        $product->color = json_encode($parsed);
                        $product->save();
                    }
                } catch (\Exception $e) {}
            } else {
                if ($costPrice > 0) $product->purchase_price_per_piece = $costPrice;
                if ($salePrice > 0) $product->sale_price_per_piece = $salePrice;
                $product->save();
            }

            $totalUpdated = 0;

            foreach ($request->stocks as $stockData) {
                $whId = (int)$stockData['warehouse_id'];
                $boxes = isset($stockData['boxes']) ? (float)$stockData['boxes'] : 0;
                $pieces = isset($stockData['pieces']) ? (float)$stockData['pieces'] : 0;

                // Calculate total pieces
                if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) {
                    $calculatedTotalPieces = ($boxes * $ppb) + $pieces;
                    $boxesCount = $boxes + ($ppb > 0 ? floor($pieces / $ppb) : 0);
                } else {
                    $calculatedTotalPieces = $pieces > 0 ? $pieces : $boxes;
                    $boxesCount = 0;
                }

                $existingQuery = OpeningStock::where('product_id', $productId)->where('warehouse_id', $whId);
                if (!empty($variantKey)) {
                    $existingQuery->where(function($q) use ($variantKey, $variantName) {
                        $q->where('variant_key', $variantKey);
                        if (!empty($variantName)) {
                            $q->orWhere('variant_name', $variantName);
                        }
                    });
                } elseif (!empty($variantName)) {
                    $existingQuery->where('variant_name', $variantName);
                } else {
                    $existingQuery->where(function($q) {
                        $q->whereNull('variant_key')->orWhere('variant_key', '');
                    });
                }
                $existingEntry = $existingQuery->first();

                $oldOpeningPieces = $existingEntry ? (float)$existingEntry->total_pieces : 0.0;
                $piecesDelta = $calculatedTotalPieces - $oldOpeningPieces;

                if ($calculatedTotalPieces > 0) {
                    if ($existingEntry) {
                        $existingEntry->boxes_quantity = $boxesCount;
                        $existingEntry->total_pieces   = $calculatedTotalPieces;
                        $existingEntry->cost_price     = $costPrice;
                        $existingEntry->sale_price     = $salePrice;
                        if ($variantKey) $existingEntry->variant_key = $variantKey;
                        if ($variantName) $existingEntry->variant_name = $variantName;
                        $existingEntry->save();
                    } else {
                        OpeningStock::create([
                            'user_id'        => auth()->id(),
                            'warehouse_id'   => $whId,
                            'product_id'     => $productId,
                            'variant_key'    => $variantKey,
                            'variant_name'   => $variantName,
                            'boxes_quantity' => $boxesCount,
                            'total_pieces'   => $calculatedTotalPieces,
                            'cost_price'     => $costPrice,
                            'sale_price'     => $salePrice,
                            'notes'          => 'Opening Stock',
                        ]);
                    }

                    $totalUpdated++;
                } else {
                    if ($existingEntry) {
                        $existingEntry->delete();
                        $totalUpdated++;
                    }
                }

                // Update WarehouseStock with delta
                if ($piecesDelta != 0) {
                    $ws = WarehouseStock::firstOrCreate(
                        ['warehouse_id' => $whId, 'product_id' => $productId],
                        ['quantity' => 0, 'total_pieces' => 0, 'remarks' => 'Opening Stock']
                    );

                    $ws->total_pieces = max(0, (float)$ws->total_pieces + $piecesDelta);
                    if ($ppb > 1 && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) {
                        $ws->quantity = round($ws->total_pieces / $ppb, 2);
                    } else {
                        $ws->quantity = $ws->total_pieces;
                    }
                    $ws->remarks = 'Opening Stock Adjusted via Edit';
                    $ws->save();

                    // If product has variants, update variant stock
                    if ($variantKey && !empty($product->color)) {
                        try {
                            $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                            if (is_array($parsed)) {
                                foreach ($parsed as &$v) {
                                    $vKey = ($v['name'] ?? $product->item_name) . '|' . ($v['size'] ?? '-') . '|' . ($v['color'] ?? '-');
                                    if ($vKey === $variantKey || ($variantName && ($v['name'] ?? '') === $variantName)) {
                                        $v['stock'] = max(0, (float)($v['stock'] ?? 0) + $piecesDelta);
                                        break;
                                    }
                                }
                                unset($v);
                                $product->color = json_encode($parsed);
                                $product->save();
                            }
                        } catch (\Exception $e) {}
                    }

                    // Movement
                    StockMovement::create([
                        'product_id' => $productId,
                        'type'       => 'adjustment',
                        'qty'        => $piecesDelta,
                        'ref_type'   => 'OPENING_STOCK_EDIT',
                        'ref_id'     => $existingEntry ? $existingEntry->id : null,
                        'note'       => "Opening Stock Updated | Warehouse #{$whId}" . ($variantName ? " | Variant: {$variantName}" : "") . " | Delta: {$piecesDelta} Pcs",
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Opening Stock updated successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Opening Stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete / Revert an Opening Stock Entry
     */
    public function destroy($id)
    {
        $this->checkPermission();

        try {
            DB::beginTransaction();

            $entry = OpeningStock::with(['product', 'warehouse'])->findOrFail($id);
            $this->revertSingleEntry($entry);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Opening stock entry reverted and deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting entry: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete / Revert Opening Stock Entries for a Product (or specific variant)
     */
    public function destroyProduct(Request $request, $productId)
    {
        $this->checkPermission();

        try {
            DB::beginTransaction();

            $variantKey  = $request->get('variant_key');
            $variantName = $request->get('variant_name');

            $query = OpeningStock::with(['product', 'warehouse'])->where('product_id', $productId);

            if (!empty($variantKey)) {
                $query->where(function($q) use ($variantKey, $variantName) {
                    $q->where('variant_key', $variantKey);
                    if (!empty($variantName)) {
                        $q->orWhere('variant_name', $variantName);
                    }
                });
            } elseif (!empty($variantName)) {
                $query->where('variant_name', $variantName);
            } else {
                $query->where(function($q) {
                    $q->whereNull('variant_key')->orWhere('variant_key', '');
                });
            }

            $entries = $query->get();
            if ($entries->isEmpty()) {
                // If not found with variant filter, check if any entries exist for product
                $entries = OpeningStock::with(['product', 'warehouse'])->where('product_id', $productId)->get();
            }

            if ($entries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No opening stock entries found for this product/variant.'
                ], 404);
            }

            foreach ($entries as $entry) {
                $this->revertSingleEntry($entry);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Opening stock entries reverted and deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product opening stock entries: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to revert a single opening stock entry and adjust warehouse stock
     */
    private function revertSingleEntry($entry)
    {
        $product = $entry->product;
        $totalPieces = (float)$entry->total_pieces;

        // Revert WarehouseStock
        $ws = WarehouseStock::where('warehouse_id', $entry->warehouse_id)
            ->where('product_id', $entry->product_id)
            ->first();

        if ($ws) {
            $ws->total_pieces = max(0, (float)$ws->total_pieces - $totalPieces);
            $ppb = (float)($product && $product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
            if ($ppb > 1 && $product && in_array($product->size_mode, ['by_cartons', 'by_size', 'by_bandal'])) {
                $ws->quantity = round($ws->total_pieces / $ppb, 2);
            } else {
                $ws->quantity = $ws->total_pieces;
            }
            $ws->remarks = 'Reversal of Opening Stock #' . $entry->id;
            $ws->save();
        }

        // Revert variant stock if applicable
        if ($entry->variant_key && $product && !empty($product->color)) {
            try {
                $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                if (is_array($parsed)) {
                    foreach ($parsed as &$v) {
                        $vKey = ($v['name'] ?? $product->item_name) . '|' . ($v['size'] ?? '-') . '|' . ($v['color'] ?? '-');
                        if ($vKey === $entry->variant_key || ($entry->variant_name && ($v['name'] ?? '') === $entry->variant_name)) {
                            $v['stock'] = max(0, (float)($v['stock'] ?? 0) - $totalPieces);
                            break;
                        }
                    }
                    unset($v);
                    $product->color = json_encode($parsed);
                    $product->save();
                }
            } catch (\Exception $e) {}
        }

        // Log reversal movement
        StockMovement::create([
            'product_id' => $entry->product_id,
            'type'       => 'adjustment',
            'qty'        => -$totalPieces,
            'ref_type'   => 'OPENING_STOCK_REVERSAL',
            'ref_id'     => $entry->id,
            'note'       => "Reversal of Opening Stock Entry #{$entry->id} for Warehouse #{$entry->warehouse_id}",
        ]);

        $entry->delete();
    }
}
