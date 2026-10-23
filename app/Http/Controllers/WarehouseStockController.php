<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\OpeningStock;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\StockEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseStockController extends Controller
{
    protected $stockService;

    public function __construct(StockEntryService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function getByWarehouse($warehouseId)
    {
        $products = WarehouseStock::with('product')
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->product->id ?? null,
                    'name' => $row->product->item_name ?? 'N/A',
                    'qty' => $row->quantity,
                ];
            });

        return response()->json($products);
    }

    public function searchWarehouses(Request $request)
    {
        $term = $request->get('q', '');

        $warehouses = Warehouse::query()
            ->select('id', 'warehouse_name')
            ->when($term, function ($query) use ($term) {
                $query->where('warehouse_name', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get();

        return response()->json($warehouses->map(function ($w) {
            return [
                'id' => $w->id,
                'text' => $w->warehouse_name,
            ];
        }));
    }

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
                'is_carton'     => false,
            ];
        }
    }

    public function index(Request $request)
    {
        $saleItems = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.sale_status', '!=', 'cancelled')
            ->select('sale_items.product_id', 'sale_items.warehouse_id', 'sale_items.total_pieces', 'sale_items.color')
            ->get();

        $saleReturnItems = DB::table('sale_return_items')
            ->select('product_id', 'warehouse_id', 'qty', 'boxes', 'loose_pieces', 'color')
            ->get();

        $purchaseItems = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereNull('purchases.deleted_at')
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

        $transfersOut = DB::table('stock_transfers')
            ->select('product_id', 'from_warehouse_id as warehouse_id', 'variant_key', 'variant_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id', 'from_warehouse_id', 'variant_key', 'variant_name')
            ->get();

        $transfersIn = DB::table('stock_transfers')
            ->where('to_shop', false)
            ->whereNotNull('to_warehouse_id')
            ->select('product_id', 'to_warehouse_id as warehouse_id', 'variant_key', 'variant_name', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id', 'to_warehouse_id', 'variant_key', 'variant_name')
            ->get();

        $adjustments = DB::table('stock_adjustments')
            ->select('warehouse_id', 'product_id', 'variant_key', 'variant_name', 'old_stock', 'new_stock')
            ->get();

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

        $warehouses = Warehouse::with(['branch', 'warehouseStocks.product.unit', 'warehouseStocks.product.category', 'warehouseStocks.product.brand'])->get();

        $allFlatItems = collect();
        $assignedProductIds = collect();

        foreach ($warehouses as $w) {
            $whStocks = WarehouseStock::with(['product.unit', 'product.category', 'product.brand'])
                ->where('warehouse_id', $w->id)
                ->where('total_pieces', '>', 0)
                ->get();

            $opStocks = OpeningStock::with(['product.unit', 'product.category', 'product.brand'])
                ->where('warehouse_id', $w->id)
                ->get();

            $items = collect();

            foreach ($whStocks as $ws) {
                $p = $ws->product;
                if (!$p) continue;

                $pOps = $opStocks->where('product_id', $p->id);
                $ppb = (float)($p->pieces_per_box > 0 ? $p->pieces_per_box : 1);
                $decodedVariants = json_decode($p->color, true) ?: [];

                if (count($decodedVariants) > 1) {
                    $variantsAdded = 0;
                    foreach ($decodedVariants as $dv) {
                        $vName = $dv['name'] ?? $p->item_name;
                        $vSize = $dv['size'] ?? '-';
                        $vColor = $dv['color'] ?? '-';
                        $vKey = $vName . '|' . $vSize . '|' . $vColor;

                        $vUnit = $dv['unit'] ?? null;
                        $vPpb = (!empty($dv['conv_factor']) && (float)$dv['conv_factor'] > 0) ? (float)$dv['conv_factor'] : $ppb;
                        $isPieceOnly = ($p->size_mode === 'by_pieces' || $vPpb <= 1 || strtolower($vUnit ?? '') === 'pcs');

                        $op = $pOps->first(function($o) use ($vKey, $vName) {
                            return ($o->variant_key === $vKey) || ($o->variant_name === $vName);
                        });
                        $opPieces = $op ? (float)$op->total_pieces : 0;

                        $vSales = 0;
                        foreach ($saleItems->where('warehouse_id', $w->id)->where('product_id', $p->id) as $si) {
                            if ($variantMatches($si->color, $vName)) {
                                $vSales += (float)$si->total_pieces;
                            }
                        }

                        $vReturns = 0;
                        foreach ($saleReturnItems->where('warehouse_id', $w->id)->where('product_id', $p->id) as $ri) {
                            if ($variantMatches($ri->color, $vName)) {
                                $rPieces = (float)($ri->qty > 0 ? $ri->qty : ((float)$ri->boxes * $vPpb));
                                $vReturns += $rPieces;
                            }
                        }

                        $vPurchases = 0;
                        foreach ($purchaseItems->where('warehouse_id', $w->id)->where('product_id', $p->id) as $pi) {
                            if ($variantMatches($pi->color, $vName)) {
                                $ppbItem = (float)($pi->pieces_per_box > 0 ? $pi->pieces_per_box : $vPpb);
                                $pPieces = ((float)$pi->boxes_qty * $ppbItem) + (float)$pi->loose_qty;
                                if ($pPieces <= 0) $pPieces = (float)$pi->qty;
                                $vPurchases += $pPieces;
                            }
                        }

                        $vTransfersOutQty = (float) ($transfersOut->where('warehouse_id', $w->id)->where('product_id', $p->id)->filter(function($t) use ($vKey, $vName) {
                            return empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName;
                        })->sum('total_qty'));
                        $vTransfersOutPieces = $vTransfersOutQty * $vPpb;

                        $vTransfersInQty = (float) ($transfersIn->where('warehouse_id', $w->id)->where('product_id', $p->id)->filter(function($t) use ($vKey, $vName) {
                            return empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName;
                        })->sum('total_qty'));
                        $vTransfersInPieces = $vTransfersInQty * $vPpb;

                        $vAdj = (float) $adjustments->where('warehouse_id', $w->id)->where('product_id', $p->id)
                            ->filter(function($a) use ($vKey, $vName) {
                                return empty($a->variant_key) || $a->variant_key === $vKey || $a->variant_name === $vName;
                            })
                            ->sum(function($a) {
                                return (float)$a->new_stock - (float)$a->old_stock;
                            });

                        $livePieces = max(0, $opPieces + $vPurchases - $vSales + $vReturns + $vTransfersInPieces - $vTransfersOutPieces + $vAdj);

                        if ($livePieces > 0) {
                            $variantsAdded++;
                            $price = 0;
                            $costPrice = 0;
                            if ($op && !empty($op->sale_price) && (float)$op->sale_price > 0) {
                                $price = (float)$op->sale_price;
                            } elseif (!empty($dv['sale_price'])) {
                                $price = (float)$dv['sale_price'];
                            } else {
                                $price = (float)($p->sale_price_per_piece ?? 0);
                            }

                            if ($op && !empty($op->cost_price) && (float)$op->cost_price > 0) {
                                $costPrice = (float)$op->cost_price;
                            } elseif (!empty($dv['purchase_price'])) {
                                $costPrice = (float)$dv['purchase_price'];
                            } else {
                                $costPrice = (float)($p->purchase_price_per_piece ?? 0);
                            }

                            $liveBoxes = $vPpb > 1 ? round($livePieces / $vPpb, 2) : $livePieces;
                            $whFmt = $this->formatPackagingQuantity($livePieces, $p, $vPpb, $vUnit);
                            $row = [
                                'product_id'    => $p->id,
                                'product_name'  => $p->item_name,
                                'item_code'     => $p->item_code,
                                'product_image' => $p->image ? asset('uploads/products/' . $p->image) : null,
                                'category_name' => $p->category?->name ?? '',
                                'brand_name'    => $p->brand?->name ?? '',
                                'variant_name'  => $vName,
                                'pieces'        => $livePieces,
                                'boxes'         => $liveBoxes,
                                'warehouse_id'  => $w->id,
                                'warehouse_name'=> $w->warehouse_name,
                                'location'      => $w->location ?: ($w->branch?->name ?? 'Main'),
                                'branch_name'   => $w->branch?->name ?? '',
                                'price'         => $price,
                                'cost_price'    => $costPrice,
                                'is_piece_only' => $isPieceOnly,
                                'unit'          => $vUnit ?: ($p->unit->name ?? 'Pcs'),
                                'formatted'     => $whFmt,
                                'ppb'           => $vPpb,
                                'size_mode'     => $p->size_mode,
                                'product_obj'   => $p,
                            ];
                            $items->push($row);
                            $allFlatItems->push($row);
                            $assignedProductIds->push($p->id);
                        }
                    }

                    // Fallback if no specific variant had positive balance but warehouse has stock
                    if ($variantsAdded === 0 && (float)$ws->total_pieces > 0) {
                        $vName = $decodedVariants[0]['name'] ?? 'Standard';
                        $vUnit = $decodedVariants[0]['unit'] ?? null;
                        $vPpb = (!empty($decodedVariants[0]['conv_factor']) && (float)$decodedVariants[0]['conv_factor'] > 0) ? (float)$decodedVariants[0]['conv_factor'] : $ppb;
                        $isPieceOnly = ($p->size_mode === 'by_pieces' || $vPpb <= 1 || strtolower($vUnit ?? '') === 'pcs');
                        $livePieces = (float)$ws->total_pieces;
                        $liveBoxes = (float)$ws->quantity > 0 ? (float)$ws->quantity : ($vPpb > 1 ? round($livePieces / $vPpb, 2) : $livePieces);
                        $whFmt = $this->formatPackagingQuantity($livePieces, $p, $vPpb, $vUnit);
                        $row = [
                            'product_id'    => $p->id,
                            'product_name'  => $p->item_name,
                            'item_code'     => $p->item_code,
                            'product_image' => $p->image ? asset('uploads/products/' . $p->image) : null,
                            'category_name' => $p->category?->name ?? '',
                            'brand_name'    => $p->brand?->name ?? '',
                            'variant_name'  => $vName,
                            'pieces'        => $livePieces,
                            'boxes'         => $liveBoxes,
                            'warehouse_id'  => $w->id,
                            'warehouse_name'=> $w->warehouse_name,
                            'location'      => $w->location ?: ($w->branch?->name ?? 'Main'),
                            'branch_name'   => $w->branch?->name ?? '',
                            'price'         => (float)($decodedVariants[0]['sale_price'] ?? $p->sale_price_per_piece ?? 0),
                            'cost_price'    => (float)($decodedVariants[0]['purchase_price'] ?? $p->purchase_price_per_piece ?? 0),
                            'is_piece_only' => $isPieceOnly,
                            'unit'          => $vUnit ?: ($p->unit->name ?? 'Pcs'),
                            'formatted'     => $whFmt,
                            'ppb'           => $vPpb,
                            'size_mode'     => $p->size_mode,
                            'product_obj'   => $p,
                        ];
                        $items->push($row);
                        $allFlatItems->push($row);
                        $assignedProductIds->push($p->id);
                    }
                } else {
                    // Single variant or no variant
                    $vName = $pOps->first()?->variant_name ?? ($decodedVariants[0]['name'] ?? 'Standard');
                    $vUnit = $decodedVariants[0]['unit'] ?? null;
                    $vPpb = (!empty($decodedVariants[0]['conv_factor']) && (float)$decodedVariants[0]['conv_factor'] > 0) ? (float)$decodedVariants[0]['conv_factor'] : $ppb;
                    $isPieceOnly = ($p->size_mode === 'by_pieces' || $vPpb <= 1 || strtolower($vUnit ?? '') === 'pcs');

                    $opPieces = (float)($pOps->sum('total_pieces'));

                    $vSales = (float)$saleItems->where('warehouse_id', $w->id)->where('product_id', $p->id)->sum('total_pieces');
                    $vReturns = 0;
                    foreach ($saleReturnItems->where('warehouse_id', $w->id)->where('product_id', $p->id) as $ri) {
                        $vReturns += (float)($ri->qty > 0 ? $ri->qty : ((float)$ri->boxes * $vPpb));
                    }
                    $vPurchases = 0;
                    foreach ($purchaseItems->where('warehouse_id', $w->id)->where('product_id', $p->id) as $pi) {
                        $ppbItem = (float)($pi->pieces_per_box > 0 ? $pi->pieces_per_box : $vPpb);
                        $pPieces = ((float)$pi->boxes_qty * $ppbItem) + (float)$pi->loose_qty;
                        if ($pPieces <= 0) $pPieces = (float)$pi->qty;
                        $vPurchases += $pPieces;
                    }
                    $vTransfersOutQty = (float)($transfersOut->where('warehouse_id', $w->id)->where('product_id', $p->id)->sum('total_qty'));
                    $vTransfersOutPieces = $vTransfersOutQty * $vPpb;
                    $vTransfersInQty = (float)($transfersIn->where('warehouse_id', $w->id)->where('product_id', $p->id)->sum('total_qty'));
                    $vTransfersInPieces = $vTransfersInQty * $vPpb;

                    $vAdj = (float) $adjustments->where('warehouse_id', $w->id)->where('product_id', $p->id)
                        ->sum(function($a) {
                            return (float)$a->new_stock - (float)$a->old_stock;
                        });

                    $hasMovements = ($vPurchases > 0 || $vSales > 0 || $vReturns > 0 || $vTransfersInPieces > 0 || $vTransfersOutPieces > 0 || $vAdj != 0);
                    // If adjustments were made, or if warehouse stock is directly maintained, prioritize accurate live stock
                    $livePieces = (float)$ws->total_pieces;

                    if ($livePieces > 0) {
                        $price = (float)($pOps->first()?->sale_price ?? $decodedVariants[0]['sale_price'] ?? $p->sale_price_per_piece ?? 0);
                        $costPrice = (float)($pOps->first()?->cost_price ?? $decodedVariants[0]['purchase_price'] ?? $p->purchase_price_per_piece ?? 0);
                        $liveBoxes = (float)$ws->quantity > 0 ? (float)$ws->quantity : ($vPpb > 1 ? round($livePieces / $vPpb, 2) : $livePieces);
                        $whFmt = $this->formatPackagingQuantity($livePieces, $p, $vPpb, $vUnit);
                        $row = [
                            'product_id'    => $p->id,
                            'product_name'  => $p->item_name,
                            'item_code'     => $p->item_code,
                            'product_image' => $p->image ? asset('uploads/products/' . $p->image) : null,
                            'category_name' => $p->category?->name ?? '',
                            'brand_name'    => $p->brand?->name ?? '',
                            'variant_name'  => $vName,
                            'pieces'        => $livePieces,
                            'boxes'         => $liveBoxes,
                            'warehouse_id'  => $w->id,
                            'warehouse_name'=> $w->warehouse_name,
                            'location'      => $w->location ?: ($w->branch?->name ?? 'Main'),
                            'branch_name'   => $w->branch?->name ?? '',
                            'price'         => $price,
                            'cost_price'    => $costPrice,
                            'is_piece_only' => $isPieceOnly,
                            'unit'          => $vUnit ?: ($p->unit->name ?? 'Pcs'),
                            'formatted'     => $whFmt,
                            'ppb'           => $vPpb,
                            'size_mode'     => $p->size_mode,
                            'product_obj'   => $p,
                        ];
                        $items->push($row);
                        $allFlatItems->push($row);
                        $assignedProductIds->push($p->id);
                    }
                }
            }

            $w->assigned_items = $items;
            $w->total_units = $items->sum('pieces');
            $w->products_count = $items->count();
        }

        // Group flat items by product_id and variant_name so a variant present across multiple warehouses shows in ONE row
        $groupedAssignedItems = $allFlatItems->groupBy(function ($item) {
            return $item['product_id'] . '_' . $item['variant_name'];
        })->map(function ($group) {
            $first = $group->first();
            $totalPieces = (float) $group->sum('pieces');
            $pObj = $first['product_obj'];
            $totalFmt = $this->formatPackagingQuantity($totalPieces, $pObj, $first['ppb'], $first['unit']);
            $rowKey = 'ws_' . $first['product_id'] . '_' . md5($first['variant_name']);

            return [
                'row_key'        => $rowKey,
                'product_id'     => $first['product_id'],
                'product_name'   => $first['product_name'],
                'item_code'      => $first['item_code'],
                'product_image'  => $first['product_image'],
                'category_name'  => $first['category_name'],
                'brand_name'     => $first['brand_name'],
                'variant_name'   => $first['variant_name'],
                'price'          => $first['price'],
                'cost_price'     => $first['cost_price'],
                'is_piece_only'  => $first['is_piece_only'],
                'unit'           => $first['unit'],
                'total_pieces'   => $totalPieces,
                'total_boxes'    => $group->sum('boxes'),
                'total_formatted'=> $totalFmt,
                'locations_count'=> $group->pluck('warehouse_id')->unique()->count(),
                'locations'      => $group->map(function ($g) {
                    return [
                        'warehouse_id'   => $g['warehouse_id'],
                        'warehouse_name' => $g['warehouse_name'],
                        'branch_name'    => $g['branch_name'],
                        'location'       => $g['location'],
                        'pieces'         => $g['pieces'],
                        'boxes'          => $g['boxes'],
                        'is_piece_only'  => $g['is_piece_only'],
                        'formatted'      => $g['formatted'],
                        'stock_value'    => (float)($g['pieces'] * $g['price']),
                    ];
                })->values()->toArray(),
            ];
        })->values();

        $productsLocationData = $groupedAssignedItems->keyBy('row_key')->toArray();

        // Summary KPIs
        $totalProducts = $assignedProductIds->unique()->count();
        $totalStockQuantity = (float) $allFlatItems->sum('pieces');
        $activeWarehousesCount = $warehouses->filter(fn($w) => $w->total_units > 0)->count();

        // KPI: Cartons / Bandals Stock
        $cartonBandalItems = $groupedAssignedItems->filter(fn($i) => empty($i['is_piece_only']));
        $totalCartonBoxes = 0;
        $totalCartonLoose = 0;
        $totalCartonPieces = 0;
        foreach ($cartonBandalItems as $ci) {
            $totalCartonPieces += (float)($ci['total_pieces'] ?? 0);
            $fmt = $ci['total_formatted'] ?? [];
            $totalCartonBoxes += (int)($fmt['cartons'] ?? 0);
            $totalCartonLoose += (int)($fmt['loose'] ?? 0);
        }

        // KPI: Pieces Only Stock
        $piecesOnlyItems = $groupedAssignedItems->filter(fn($i) => !empty($i['is_piece_only']));
        $totalPiecesOnlyStock = (float) $piecesOnlyItems->sum('total_pieces');

        return view('admin_panel.warehouses.warehouse_stocks.index', compact(
            'warehouses',
            'totalProducts',
            'totalStockQuantity',
            'activeWarehousesCount',
            'totalCartonBoxes',
            'totalCartonLoose',
            'totalCartonPieces',
            'totalPiecesOnlyStock',
            'groupedAssignedItems',
            'productsLocationData'
        ));
    }

    public function show($id)
    {
        return redirect()->route('warehouse_stocks.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'total_pieces' => 'required|integer|min:0',
            'total_box' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $this->stockService->addStock(
                $validated['warehouse_id'],
                $validated['product_id'],
                $validated['total_pieces'],
                $validated['total_box'],
                $request->input('remarks')
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: '.$e->getMessage(),
            ], 500);
        }
    }

    public function editData($id)
    {
        $stock = WarehouseStock::with(['product', 'warehouse'])->findOrFail($id);

        $piecesPerBox = $stock->product->pieces_per_box ?? 0;

        $boxes = 0;
        $loose = $stock->total_pieces;

        if ($piecesPerBox > 0) {
            $boxes = floor($stock->total_pieces / $piecesPerBox);
            $loose = $stock->total_pieces % $piecesPerBox;
        }

        return response()->json([
            'id' => $stock->id,
            'warehouse_id' => $stock->warehouse_id,
            'warehouse_name' => $stock->warehouse->warehouse_name ?? '',
            'product_id' => $stock->product_id,
            'product_name' => $stock->product->item_name ?? '',
            'product_code' => $stock->product->item_code ?? '',
            'pieces_per_box' => $piecesPerBox,
            'total_pieces' => $stock->total_pieces,
            'image' => ($stock->product && $stock->product->image) ? asset('uploads/products/'.$stock->product->image) : null,
            'remarks' => $stock->remarks,
        ]);
    }

    public function update(Request $request, $id)
    {
        $warehouseStock = WarehouseStock::findOrFail($id);

        $request->validate([
            'total_pieces' => 'required|integer|min:0',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $warehouseStock) {
            $oldQty = $warehouseStock->total_pieces;
            $newQty = $request->total_pieces;
            $delta = $newQty - $oldQty;

            if ($delta != 0) {
                $warehouseStock->total_pieces = $newQty;
                $warehouseStock->remarks = $request->remarks;
                $warehouseStock->save();

                DB::table('stock_movements')->insert([
                    'product_id' => $warehouseStock->product_id,
                    'warehouse_id' => $warehouseStock->warehouse_id,
                    'type' => 'adjustment',
                    'qty' => $delta,
                    'ref_type' => 'MANUAL_EDIT_MODAL',
                    'ref_id' => $warehouseStock->id,
                    'note' => 'Manual Stock Edit via Modal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully!',
        ]);
    }

    public function destroy(WarehouseStock $warehouseStock)
    {
        $warehouseStock->delete();

        return back()->with('success', 'Stock deleted successfully.');
    }

    public function searchProducts(Request $request)
    {
        $term = $request->get('q', '');

        $products = Product::query()
            ->select('id', 'item_name', 'item_code', 'pieces_per_box', 'image')
            ->when($term, function ($query) use ($term) {
                $query->where('item_name', 'like', "%{$term}%")
                    ->orWhere('item_code', 'like', "%{$term}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => "{$p->item_code} - {$p->item_name}",
                'item_name' => $p->item_name,
                'item_code' => $p->item_code,
                'pieces_per_box' => $p->pieces_per_box ?? 0,
                'image' => $p->image ? asset('uploads/products/'.$p->image) : null,
            ];
        }));
    }

    public function getWarehouseStock(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        return response()->json([
            'total_pieces' => $stock ? $stock->total_pieces : 0,
        ]);
    }
}
