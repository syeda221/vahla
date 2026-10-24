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
                $q->with(['product.unit', 'product.category_relation']);
                if ($search) {
                    $q->whereHas('product', fn($pq) => $pq->where('item_name', 'like', "%{$search}%")->orWhere('item_code', 'like', "%{$search}%"));
                }
                $q->orderBy('total_pieces', 'DESC');
            }])
            ->orderByRaw('is_shop DESC, warehouse_name ASC');

        $warehousesWithStock = $warehousesQuery->get();

        // 2. Flat Stocks Query (for products view or pagination)
        $flatQuery = WarehouseStock::with(['warehouse', 'product.unit', 'product.category_relation']);

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

        $stocks = $flatQuery->orderBy('total_pieces', 'DESC')->paginate(25)->appends($request->query());

        // Overall Statistics
        $totalCount         = WarehouseStock::count();
        $shopPieces         = (float) WarehouseStock::whereHas('warehouse', fn($q) => $q->where('is_shop', 1))->sum('total_pieces');
        $shopCount          = WarehouseStock::whereHas('warehouse', fn($q) => $q->where('is_shop', 1))->count();
        $godownPieces       = (float) WarehouseStock::whereHas('warehouse', fn($q) => $q->where('is_shop', 0))->sum('total_pieces');
        $godownCount        = WarehouseStock::whereHas('warehouse', fn($q) => $q->where('is_shop', 0))->count();
        $totalPiecesOverall = (float) WarehouseStock::sum('total_pieces');

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

                    // 1. Update Product Pricing & Alert Qty if provided
                    $product = Product::find($productId);
                    if ($product) {
                        if (isset($row['wholesale_price']) && is_numeric($row['wholesale_price']) && $row['wholesale_price'] > 0) {
                            $product->wholesale_price = (float)$row['wholesale_price'];
                        }
                        if (isset($row['retail_price']) && is_numeric($row['retail_price']) && $row['retail_price'] > 0) {
                            $product->sale_price_per_piece = (float)$row['retail_price'];
                        }
                        if (isset($row['alert_quantity']) && is_numeric($row['alert_quantity'])) {
                            $product->alert_quantity = (float)$row['alert_quantity'];
                        }
                        $product->save();
                    }

                    // 2. Allocate Stock per Location
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
        $stock = WarehouseStock::with(['warehouse', 'product.unit'])->findOrFail($id);
        return view('admin_panel.warehouses.warehouse_stocks.edit', compact('stock'));
    }

    public function editData($id)
    {
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
        $warehouseStock->delete();
        return back()->with('success', 'Stock deleted successfully.');
    }

    public function searchProducts(Request $request)
    {
        $term = $request->get('q', '');

        $products = Product::with('unit')
            ->select('id', 'item_name', 'item_code', 'unit_id', 'wholesale_price', 'sale_price_per_piece', 'purchase_price_per_piece', 'alert_quantity', 'pieces_per_box', 'image')
            ->when($term, function ($query) use ($term) {
                $query->where('item_name', 'like', "%{$term}%")
                    ->orWhere('item_code', 'like', "%{$term}%");
            })
            ->limit(30)
            ->get();

        return response()->json($products->map(function ($p) {
            $unitName = $p->unit_name;
            return [
                'id'              => $p->id,
                'text'            => "{$p->item_code} — {$p->item_name} [{$unitName}]",
                'item_name'       => $p->item_name,
                'item_code'       => $p->item_code,
                'unit_name'       => $unitName,
                'wholesale_price' => (float)($p->wholesale_price ?? ($p->purchase_price_per_piece ?? 0)),
                'retail_price'    => (float)($p->sale_price_per_piece ?? 0),
                'alert_quantity'  => (float)($p->alert_quantity ?? 0),
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
}
