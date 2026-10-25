<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $fromWh = $request->get('from_warehouse_id');
        $toWh   = $request->get('to_warehouse_id');

        $query = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product.unit', 'product.category_relation'])
            ->when($fromWh, fn($q) => $q->where('from_warehouse_id', $fromWh))
            ->when($toWh, fn($q) => $q->where('to_warehouse_id', $toWh))
            ->when($search, function($q) use ($search) {
                $q->whereHas('product', function($pq) use ($search) {
                    $pq->where('item_name', 'like', "%{$search}%")
                       ->orWhere('item_code', 'like', "%{$search}%");
                })->orWhere('remarks', 'like', "%{$search}%");
            })
            ->orderBy('id', 'DESC');

        $transfers = $query->paginate(30)->appends($request->query());

        // KPI Statistics
        $totalTransfersCount = StockTransfer::count();
        $totalQuantitySum    = (float)StockTransfer::sum('quantity');
        $toShopCount         = StockTransfer::where('to_shop', 1)->count();
        $toGodownCount       = StockTransfer::where('to_shop', 0)->count();

        $warehouses = Warehouse::orderByRaw('is_shop DESC, warehouse_name ASC')->get();

        return view('admin_panel.warehouses.stock_transfers.index', compact(
            'transfers', 'warehouses', 'totalTransfersCount', 'totalQuantitySum', 'toShopCount', 'toGodownCount', 'search', 'fromWh', 'toWh'
        ));
    }

    public function create()
    {
        $warehouses = Warehouse::orderByRaw('is_shop DESC, warehouse_name ASC')->get();
        $shops      = Warehouse::where('is_shop', 1)->orderBy('warehouse_name')->get();
        $godowns    = Warehouse::where('is_shop', 0)->orderBy('warehouse_name')->get();
        $defaultShop = Warehouse::defaultShop();

        $products = Product::select('id', 'item_name', 'item_code', 'unit_id', 'size_mode', 'pieces_per_box', 'image')
            ->with('unit:id,name')
            ->orderBy('item_name')
            ->get();

        $transferNo = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));

        $recentTransfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'product.unit'])
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get();

        return view('admin_panel.warehouses.stock_transfers.create', compact(
            'warehouses', 'shops', 'godowns', 'defaultShop', 'products', 'transferNo', 'recentTransfers'
        ));
    }

    public function getProductSources($productId)
    {
        $product = Product::with('unit')->findOrFail($productId);
        $ppb = ($product->pieces_per_box > 0) ? (float)$product->pieces_per_box : 1;
        $unitName = $product->unit_name ?? ($product->unit->name ?? 'Pcs');

        $stocks = WarehouseStock::with('warehouse')
            ->where('product_id', $productId)
            ->get();

        $allWarehouses = Warehouse::orderByRaw('is_shop DESC, warehouse_name ASC')->get();

        $sources = [];
        foreach ($allWarehouses as $wh) {
            $stock = $stocks->firstWhere('warehouse_id', $wh->id);
            $avail = (float)($stock ? ($stock->total_pieces > 0 ? $stock->total_pieces : ($stock->quantity * $ppb)) : 0);
            $fmt = rtrim(rtrim(number_format($avail, 2, '.', ''), '0'), '.');
            if ($fmt === '' || $fmt === '-0') $fmt = '0';

            $sources[] = [
                'warehouse_id'     => $wh->id,
                'warehouse_name'   => $wh->warehouse_name,
                'is_shop'          => (int)$wh->is_shop,
                'available_pieces' => $avail,
                'formatted_qty'    => $fmt,
                'unit_name'        => $unitName,
                'has_stock'        => $avail > 0,
            ];
        }

        return response()->json([
            'product_id' => $product->id,
            'item_name'  => $product->item_name,
            'item_code'  => $product->item_code,
            'unit_name'  => $unitName,
            'sources'    => $sources,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Destination Parsing
        $destType = $request->input('destination_type', 'warehouse');
        $toShop   = ($destType === 'shop' || $request->boolean('to_shop'));

        $destWhId = $request->input('to_warehouse_id');
        if ($toShop && empty($destWhId)) {
            $destWhId = Warehouse::defaultShop()->id ?? 1;
        }

        if (empty($destWhId)) {
            return back()->withInput()->with('error', 'Please select a destination warehouse or branch shop.');
        }

        $destWarehouse = Warehouse::findOrFail($destWhId);

        // 2. Normalize Items List (handles both new 'items' array and legacy parallel arrays)
        $rawItems = $request->input('items', []);

        if (empty($rawItems) && $request->filled('product_id')) {
            $headerFromWh = $request->input('from_warehouse_id');
            foreach ((array)$request->product_id as $idx => $pid) {
                $qty = (float)($request->quantity[$idx] ?? 0);
                if ($qty > 0) {
                    $rawItems[] = [
                        'product_id'        => $pid,
                        'from_warehouse_id' => $request->from_warehouse_id[$idx] ?? $headerFromWh,
                        'quantity'          => $qty,
                    ];
                }
            }
        }

        // Filter valid rows
        $items = [];
        foreach ($rawItems as $row) {
            $pid    = !empty($row['product_id']) ? (int)$row['product_id'] : null;
            $fromWh = !empty($row['from_warehouse_id']) ? (int)$row['from_warehouse_id'] : null;
            $qty    = (float)($row['quantity'] ?? 0);

            if ($pid && $fromWh && $qty > 0) {
                $items[] = [
                    'product_id'        => $pid,
                    'from_warehouse_id' => $fromWh,
                    'quantity'          => $qty,
                ];
            }
        }

        if (empty($items)) {
            return back()->withInput()->with('error', 'Please add at least one product with a valid source and quantity greater than 0.');
        }

        // Validate that no item transfers to the same source
        foreach ($items as $item) {
            if ($item['from_warehouse_id'] == $destWhId) {
                $product = Product::find($item['product_id']);
                $pName   = $product ? $product->item_name : 'Item #'.$item['product_id'];
                return back()->withInput()->with('error', "Cannot transfer '{$pName}': Source location and destination location are the same ({$destWarehouse->warehouse_name}).");
            }
        }

        $remarks    = $request->input('remarks');
        $transferNo = $request->input('transfer_no') ?: ('TRF-' . date('Ymd-His'));

        try {
            DB::transaction(function () use ($items, $destWhId, $destWarehouse, $toShop, $remarks, $transferNo) {
                foreach ($items as $item) {
                    $productId = $item['product_id'];
                    $fromWhId  = $item['from_warehouse_id'];
                    $qty       = (float)$item['quantity'];

                    $product = Product::findOrFail($productId);
                    $ppb = ($product->pieces_per_box > 0) ? (float)$product->pieces_per_box : 1;
                    $fromWarehouse = Warehouse::findOrFail($fromWhId);

                    // 1. Lock and Verify Source Stock
                    $sourceStock = WarehouseStock::where('warehouse_id', $fromWhId)
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    $availPieces = (float)($sourceStock ? ($sourceStock->total_pieces > 0 ? $sourceStock->total_pieces : ($sourceStock->quantity * $ppb)) : 0);

                    if (!$sourceStock || $availPieces < $qty) {
                        throw new \Exception("Insufficient stock for '{$product->item_name}' in {$fromWarehouse->warehouse_name}. Available: {$availPieces}, Requested: {$qty}");
                    }

                    // 2. Decrement Source Stock
                    $sourceStock->total_pieces = max(0, $availPieces - $qty);
                    $sourceStock->quantity     = round($sourceStock->total_pieces / $ppb, 2);
                    $sourceStock->save();

                    // 3. Increment Destination Stock
                    $destStock = WarehouseStock::firstOrCreate(
                        [
                            'warehouse_id' => $destWhId,
                            'product_id'   => $productId,
                        ],
                        [
                            'total_pieces' => 0,
                            'quantity'     => 0,
                            'price'        => $sourceStock->price ?? 0,
                        ]
                    );

                    $destAvail = (float)($destStock->total_pieces > 0 ? $destStock->total_pieces : ($destStock->quantity * $ppb));
                    $destStock->total_pieces = $destAvail + $qty;
                    $destStock->quantity     = round($destStock->total_pieces / $ppb, 2);
                    $destStock->save();

                    // 4. Record StockTransfer
                    $transfer = StockTransfer::create([
                        'from_warehouse_id' => $fromWhId,
                        'to_warehouse_id'   => $destWhId,
                        'to_shop'           => $toShop ? 1 : 0,
                        'product_id'        => $productId,
                        'quantity'          => $qty,
                        'remarks'           => $remarks ?: "Stock Transfer {$transferNo}",
                    ]);

                    // 5. Audit Log Movement (OUT from Source)
                    DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'type'       => 'out',
                        'qty'        => -1 * abs($qty),
                        'ref_type'   => 'TRANSFER_OUT',
                        'ref_id'     => $transfer->id,
                        'note'       => "Stock Transfer to {$destWarehouse->warehouse_name} ({$transferNo})",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // 6. Audit Log Movement (IN to Destination)
                    DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'type'       => 'in',
                        'qty'        => abs($qty),
                        'ref_type'   => 'TRANSFER_IN',
                        'ref_id'     => $transfer->id,
                        'note'       => "Stock Transfer from {$fromWarehouse->warehouse_name} ({$transferNo})",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully transferred " . count($items) . " item(s) to {$destWarehouse->warehouse_name}!",
                ]);
            }

            return redirect()->route('stock_transfers.index')
                ->with('success', "Successfully transferred " . count($items) . " item(s) to {$destWarehouse->warehouse_name}!");

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Transfer Failed: ' . $e->getMessage());
        }
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        return back()->with('error', 'Deleting completed transfers is not allowed to preserve stock audit integrity.');
    }

    public function getStockQuantity(Request $request)
    {
        $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        $qty = $stock ? ($stock->total_pieces > 0 ? (float)$stock->total_pieces : (float)$stock->quantity) : 0;

        return response()->json([
            'quantity' => $qty,
        ]);
    }

    public function getProductsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        if (!$warehouseId) {
            return response()->json([]);
        }

        $products = Product::join('warehouse_stocks', 'products.id', '=', 'warehouse_stocks.product_id')
            ->where('warehouse_stocks.warehouse_id', $warehouseId)
            ->where(function ($q) {
                $q->where('warehouse_stocks.total_pieces', '>', 0)
                  ->orWhere('warehouse_stocks.quantity', '>', 0);
            })
            ->select('products.id', 'products.item_name', 'products.item_code')
            ->distinct()
            ->get();

        return response()->json($products);
    }
}
