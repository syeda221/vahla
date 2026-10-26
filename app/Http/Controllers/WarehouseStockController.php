<?php

namespace App\Http\Controllers;

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
                    'id' => $row->product->id,
                    'name' => $row->product->item_name,
                    'qty' => $row->quantity,
                ];
            });

        return response()->json($products);
    }

    public function searchWarehouses(Request $request)
    {
        $term = $request->get('q', '');

        $warehouses = Warehouse::query()
            ->select('id', 'warehouse_name', 'is_shop')
            ->when($term, function ($query) use ($term) {
                $query->where('warehouse_name', 'like', "%{$term}%");
            })
            ->orderByRaw('is_shop DESC, warehouse_name ASC')
            ->limit(20)
            ->get();

        return response()->json($warehouses->map(function ($w) {
            $prefix = $w->is_shop ? '🏪 [Shop] ' : '🏢 [Godown] ';
            return [
                'id' => $w->id,
                'text' => $prefix . $w->warehouse_name,
            ];
        }));
    }

    public function index(Request $request)
    {
        if (! auth()->user()->can('warehouse.stock.view')) {
            abort(403, 'Unauthorized action. You do not have permission to view Warehouse Stock.');
        }

        $viewMode     = $request->get('view', 'warehouses'); // 'warehouses' or 'products'
        $locationType = $request->get('location_type', 'all'); // 'all', 'shop', 'godown'
        $warehouseId  = $request->get('warehouse_id', 'all');
        $search       = $request->get('search');

        // All available warehouses for dropdowns
        $warehouses = Warehouse::orderByRaw('is_shop DESC, warehouse_name ASC')->get();

        // 1. Warehouses View with child stocks (Image 1 Style Accordions)
        $warehousesQuery = Warehouse::query()
            ->when($locationType === 'shop', fn($q) => $q->where('is_shop', 1))
            ->when($locationType === 'godown', fn($q) => $q->where('is_shop', 0))
            ->when($warehouseId && $warehouseId !== 'all', fn($q) => $q->where('id', $warehouseId))
            ->withCount(['warehouseStocks as total_products_count' => function ($q) use ($search) {
                if ($search) {
                    $q->whereHas('product', fn($pq) => $pq->where('item_name', 'like', "%{$search}%")->orWhere('item_code', 'like', "%{$search}%"));
                }
            }])
            ->withSum(['warehouseStocks as total_units_sum' => function ($q) use ($search) {
                if ($search) {
                    $q->whereHas('product', fn($pq) => $pq->where('item_name', 'like', "%{$search}%")->orWhere('item_code', 'like', "%{$search}%"));
                }
            }], 'total_pieces')
            ->with(['warehouseStocks' => function ($q) use ($search) {
                $q->select('id', 'warehouse_id', 'product_id', 'total_pieces', 'quantity', 'remarks', 'created_at')
                  ->with([
                      'product' => function ($pq) {
                          $pq->select('id', 'item_name', 'item_code', 'unit_id', 'size_mode', 'category_id', 'image');
                      },
                      'product.unit:id,name',
                      'product.category_relation:id,name'
                  ]);
                if ($search) {
                    $q->whereHas('product', fn($pq) => $pq->where('item_name', 'like', "%{$search}%")->orWhere('item_code', 'like', "%{$search}%"));
                }
                $q->orderBy('total_pieces', 'DESC');
            }])
            ->orderByRaw('is_shop DESC, warehouse_name ASC');

        $warehousesWithStock = $warehousesQuery->get();

        // 2. Flat Stocks Query (for products view or pagination)
        $flatQuery = WarehouseStock::select('id', 'warehouse_id', 'product_id', 'total_pieces', 'quantity', 'remarks')
            ->with([
                'warehouse:id,warehouse_name,is_shop',
                'product:id,item_name,item_code,unit_id,size_mode,category_id,image',
                'product.unit:id,name',
                'product.category_relation:id,name'
            ]);

        if ($locationType === 'shop') {
            $flatQuery->whereHas('warehouse', fn($q) => $q->where('is_shop', 1));
        } elseif ($locationType === 'godown') {
            $flatQuery->whereHas('warehouse', fn($q) => $q->where('is_shop', 0));
        }

        if ($warehouseId && $warehouseId !== 'all') {
            $flatQuery->where('warehouse_id', $warehouseId);
        }

        if ($search) {
            $flatQuery->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('item_name', 'like', "%{$search}%")
                       ->orWhere('item_code', 'like', "%{$search}%");
                })->orWhereHas('warehouse', function ($wq) use ($search) {
                    $wq->where('warehouse_name', 'like', "%{$search}%");
                });
            });
        }

        $stocks = $flatQuery->orderBy('total_pieces', 'DESC')->paginate(50)->appends($request->query());

        // Overall Statistics (Optimized Single Combined Query)
        $stockStats = DB::table('warehouse_stocks')
            ->join('warehouses', 'warehouse_stocks.warehouse_id', '=', 'warehouses.id')
            ->selectRaw("
                COUNT(warehouse_stocks.id) as total_count,
                COALESCE(SUM(warehouse_stocks.total_pieces), 0) as total_pieces,
                COALESCE(SUM(CASE WHEN warehouses.is_shop = 1 THEN warehouse_stocks.total_pieces ELSE 0 END), 0) as shop_pieces,
                COUNT(CASE WHEN warehouses.is_shop = 1 THEN warehouse_stocks.id ELSE NULL END) as shop_count,
                COALESCE(SUM(CASE WHEN warehouses.is_shop = 0 THEN warehouse_stocks.total_pieces ELSE 0 END), 0) as godown_pieces,
                COUNT(CASE WHEN warehouses.is_shop = 0 THEN warehouse_stocks.id ELSE NULL END) as godown_count
            ")
            ->first();

        $totalCount         = (int)($stockStats->total_count ?? 0);
        $totalPiecesOverall = (float)($stockStats->total_pieces ?? 0);
        $shopPieces         = (float)($stockStats->shop_pieces ?? 0);
        $shopCount          = (int)($stockStats->shop_count ?? 0);
        $godownPieces       = (float)($stockStats->godown_pieces ?? 0);
        $godownCount        = (int)($stockStats->godown_count ?? 0);

        return view('admin_panel.warehouses.warehouse_stocks.index', compact(
            'viewMode', 'warehouses', 'warehousesWithStock', 'stocks', 'locationType', 'warehouseId', 'search',
            'totalCount', 'shopPieces', 'shopCount', 'godownPieces', 'godownCount', 'totalPiecesOverall'
        ));
    }

    public function show($id)
    {
        return redirect()->route('warehouse_stocks.index');
    }

    public function create()
    {
        if (! auth()->user()->can('opening.stock.view') && ! auth()->user()->can('opening.stock.create') && ! auth()->user()->can('warehouse.stock.create') && ! auth()->user()->can('warehouse.stock.view')) {
            abort(403, 'Unauthorized action. You do not have permission to access Opening Stock.');
        }

        $warehouses = Warehouse::orderByRaw('is_shop DESC, warehouse_name ASC')->get();

        $recentMovements = DB::table('stock_movements')
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->leftJoin('warehouse_stocks', 'warehouse_stocks.id', '=', 'stock_movements.ref_id')
            ->leftJoin('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->leftJoin('units', 'units.id', '=', 'products.unit_id')
            ->where('stock_movements.ref_type', 'OPENING_STOCK')
            ->select('stock_movements.*', 'products.item_name', 'products.item_code', 'units.name as unit_name', 'warehouses.warehouse_name', 'warehouses.is_shop')
            ->orderBy('stock_movements.id', 'DESC')
            ->limit(10)
            ->get();

        return view('admin_panel.warehouses.warehouse_stocks.create', compact('warehouses', 'recentMovements'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('opening.stock.create') && ! auth()->user()->can('warehouse.stock.create')) {
            abort(403, 'Unauthorized action. You do not have permission to save Opening Stock.');
        }

        // Check if bulk rows submitted (Image 1 ERP Standard style)
        $rows = $request->input('rows', []);
        $targetWarehouseId = $request->input('target_warehouse_id', 1);

        // If single product format was submitted (backward compatibility)
        if ($request->filled('product_id') && empty($rows)) {
            $qtyVal = (float)$request->input('quantity', $request->input('total_pieces', 0));
            $rows = [
                [
                    'product_id'      => $request->input('product_id'),
                    'wholesale_price' => $request->input('wholesale_price'),
                    'retail_price'    => $request->input('retail_price'),
                    'alert_quantity'  => $request->input('alert_quantity'),
                    'locations'       => [
                        [
                            'warehouse_id' => $request->input('warehouse_id', $targetWarehouseId),
                            'quantity'     => $qtyVal,
                        ]
                    ]
                ]
            ];
        }

        if (empty($rows)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No products selected.'], 422);
            }
            return back()->with('error', 'Please add at least one product row with quantity.');
        }

        try {
            $savedCount = 0;

            DB::transaction(function () use ($rows, $targetWarehouseId, &$savedCount, $request) {
                foreach ($rows as $row) {
                    $productId = $row['product_id'] ?? null;
                    if (!$productId) {
                        continue;
                    }

                    // Allocate Stock per Location (Product pricing & alert details are unchangeable reference data)
                    $locations = $row['locations'] ?? [];
                    if (empty($locations)) {
                        $locations = [
                            [
                                'warehouse_id' => $targetWarehouseId,
                                'quantity'     => (float)($row['quantity'] ?? 0),
                            ]
                        ];
                    }

                    foreach ($locations as $loc) {
                        $whId = !empty($loc['warehouse_id']) ? (int)$loc['warehouse_id'] : (int)$targetWarehouseId;
                        $qty  = (float)($loc['quantity'] ?? 0);

                        if ($qty > 0 && $whId) {
                            $this->stockService->addStock(
                                $whId,
                                (int)$productId,
                                $qty,
                                $qty,
                                $request->input('remarks') ?: "Opening Stock Manager Entry"
                            );
                            $savedCount++;
                        }
                    }
                }
            });

            if ($savedCount === 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Please enter a quantity greater than 0.'], 422);
                }
                return back()->with('error', 'Please enter a quantity greater than 0 for your products.');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully saved opening stock for {$savedCount} allocation(s)!",
                ]);
            }

            return redirect()->route('warehouse_stocks.index')
                ->with('success', "Successfully recorded opening stock for {$savedCount} allocation(s)!");

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error adding opening stock: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Error adding stock: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (! auth()->user()->can('opening.stock.edit') && ! auth()->user()->can('warehouse.stock.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $stock = WarehouseStock::with(['warehouse', 'product.unit'])->findOrFail($id);
        return view('admin_panel.warehouses.warehouse_stocks.edit', compact('stock'));
    }

    public function editData($id)
    {
        if (! auth()->user()->can('opening.stock.edit') && ! auth()->user()->can('warehouse.stock.edit') && ! auth()->user()->can('warehouse.stock.view')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $stock = WarehouseStock::with(['product.unit', 'warehouse'])->findOrFail($id);

        return response()->json([
            'id'             => $stock->id,
            'warehouse_id'   => $stock->warehouse_id,
            'warehouse_name' => $stock->warehouse->warehouse_name ?? '',
            'is_shop'        => (int)($stock->warehouse->is_shop ?? 0),
            'product_id'     => $stock->product_id,
            'product_name'   => $stock->product->item_name,
            'product_code'   => $stock->product->item_code,
            'unit_name'      => $stock->product->unit->name ?? 'Pcs',
            'total_pieces'   => (float)$stock->total_pieces,
            'quantity'       => (float)$stock->quantity,
            'image'          => $stock->product->image ? asset('uploads/products/'.$stock->product->image) : null,
            'remarks'        => $stock->remarks,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('opening.stock.edit') && ! auth()->user()->can('warehouse.stock.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $warehouseStock = WarehouseStock::findOrFail($id);

        $quantityVal = $request->input('quantity', $request->input('total_pieces'));
        $request->merge(['quantity_clean' => $quantityVal]);

        $request->validate([
            'quantity_clean' => 'required|numeric',
            'remarks'        => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $warehouseStock) {
            $oldQty = (float) $warehouseStock->total_pieces;
            $newQty = (float) $request->input('quantity_clean');
            $delta  = $newQty - $oldQty;

            $warehouseStock->total_pieces = $newQty;
            $warehouseStock->quantity     = $newQty;
            if ($request->filled('remarks')) {
                $warehouseStock->remarks = $request->remarks;
            }
            $warehouseStock->save();

            if ($delta != 0) {
                DB::table('stock_movements')->insert([
                    'product_id'   => $warehouseStock->product_id,
                    'type'         => $delta > 0 ? 'in' : 'adjustment',
                    'qty'          => $delta,
                    'ref_type'     => 'STOCK_ADJUSTMENT',
                    'ref_id'       => $warehouseStock->id,
                    'note'         => $request->remarks ?: 'Manual Stock Adjustment',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully!',
            ]);
        }

        return redirect()->route('warehouse_stocks.index')->with('success', 'Stock updated successfully!');
    }

    public function destroy(WarehouseStock $warehouseStock)
    {
        if (! auth()->user()->can('opening.stock.delete') && ! auth()->user()->can('warehouse.stock.delete')) {
            abort(403, 'Unauthorized action.');
        }

        $warehouseStock->delete();
        return back()->with('success', 'Stock deleted successfully.');
    }

    public function searchProducts(Request $request)
    {
        $term = $request->get('q', '');

        $products = Product::with('unit')
            ->select('id', 'item_name', 'item_code', 'unit_id', 'size_mode', 'wholesale_price', 'sale_price_per_piece', 'purchase_price_per_piece', 'alert_quantity', 'pieces_per_box', 'image')
            ->when($term, function ($query) use ($term) {
                $query->where('item_name', 'like', "%{$term}%")
                    ->orWhere('item_code', 'like', "%{$term}%");
            })
            ->limit(30)
            ->get();

        return response()->json($products->map(function ($p) {
            $unitName = $p->unit_name;
            $wholesale = (float)($p->wholesale_price > 0 ? $p->wholesale_price : ($p->purchase_price_per_piece ?? 0));
            $retail = (float)($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : 0);
            $alert = (float)($p->alert_quantity ?? 0);
            return [
                'id'              => $p->id,
                'text'            => "{$p->item_code} — {$p->item_name} [{$unitName}]",
                'item_name'       => $p->item_name,
                'item_code'       => $p->item_code,
                'unit_name'       => $unitName,
                'wholesale_price' => $wholesale > 0 ? rtrim(rtrim(number_format($wholesale, 2, '.', ''), '0'), '.') : '',
                'retail_price'    => $retail > 0 ? rtrim(rtrim(number_format($retail, 2, '.', ''), '0'), '.') : '',
                'alert_quantity'  => $alert > 0 ? rtrim(rtrim(number_format($alert, 2, '.', ''), '0'), '.') : '',
                'image'           => $p->image ? asset('uploads/products/'.$p->image) : null,
            ];
        }));
    }

    public function getWarehouseStock(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
        ]);

        $stock = WarehouseStock::with('product.unit')
            ->where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        $product = Product::with('unit')->find($request->product_id);
        $unitName = $stock?->product?->unit_name ?? ($product?->unit_name ?? 'Kg');

        $rawQty = $stock ? (float)$stock->total_pieces : 0.0;
        $formatted = rtrim(rtrim(number_format($rawQty, 2, '.', ''), '0'), '.');
        if ($formatted === '' || $formatted === '-0') {
            $formatted = '0';
        }

        return response()->json([
            'exists'          => (bool)$stock,
            'total_pieces'    => $rawQty,
            'formatted_stock' => $formatted,
            'unit_name'       => $unitName,
        ]);
    }

    public function getProductDetails($id)
    {
        $product = Product::with('unit')->findOrFail($id);
        $unitName = $product->unit_name;
        $wholesale = (float)($product->wholesale_price > 0 ? $product->wholesale_price : ($product->purchase_price_per_piece ?? 0));
        $retail = (float)($product->sale_price_per_piece > 0 ? $product->sale_price_per_piece : 0);
        $alert = (float)($product->alert_quantity ?? 0);

        return response()->json([
            'id'              => $product->id,
            'item_name'       => $product->item_name,
            'item_code'       => $product->item_code,
            'unit_name'       => $unitName,
            'wholesale_price' => $wholesale > 0 ? rtrim(rtrim(number_format($wholesale, 2, '.', ''), '0'), '.') : '',
            'retail_price'    => $retail > 0 ? rtrim(rtrim(number_format($retail, 2, '.', ''), '0'), '.') : '',
            'alert_quantity'  => $alert > 0 ? rtrim(rtrim(number_format($alert, 2, '.', ''), '0'), '.') : '',
        ]);
    }
}
