<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with('fromWarehouse', 'toWarehouse', 'product')->get();

        return view('admin_panel.warehouses.stock_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        return view('admin_panel.warehouses.stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|integer',
            'product_id' => 'required|array',
            'product_id.*' => 'required|integer|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
        ]);

        $fromWarehouse = $request->from_warehouse_id;
        $toShop = $request->to_shop ? true : false;
        $destWhId = $toShop ? (\App\Models\Warehouse::defaultShop()->id ?? 1) : $request->to_warehouse_id;
        $remarks = $request->remarks;

        // Aggregate quantities by product
        $groupped = [];
        foreach ($request->product_id as $index => $pid) {
            $qty = (int) ($request->quantity[$index] ?? 0);
            if ($qty > 0) {
                if (! isset($groupped[$pid])) {
                    $groupped[$pid] = 0;
                }
                $groupped[$pid] += $qty;
            }
        }

        if ($fromWarehouse == $destWhId) {
            return back()->with('error', 'Source and Destination warehouses cannot be the same.');
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($fromWarehouse, $destWhId, $toShop, $remarks, $groupped) {

                foreach ($groupped as $productId => $totalQty) {
                    $product = Product::find($productId);
                    $productName = $product->item_name ?? 'Product #'.$productId;
                    $ppb = ($product && $product->pieces_per_box > 0) ? (float)$product->pieces_per_box : 1;

                    // Check source stock
                    $sourceStock = WarehouseStock::where('warehouse_id', $fromWarehouse)
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    $availPieces = (float)($sourceStock ? ($sourceStock->total_pieces > 0 ? $sourceStock->total_pieces : ($sourceStock->quantity * $ppb)) : 0);

                    if (! $sourceStock || $availPieces < $totalQty) {
                        throw new \Exception("Insufficient stock for {$productName}. Requested Total: {$totalQty}, Available: {$availPieces}");
                    }

                    // Reduce stock from source
                    $sourceStock->total_pieces = max(0, $availPieces - $totalQty);
                    $sourceStock->quantity = round($sourceStock->total_pieces / $ppb, 2);
                    $sourceStock->save();

                    // Increase stock at destination
                    $destStock = WarehouseStock::firstOrCreate(
                        [
                            'warehouse_id' => $destWhId,
                            'product_id' => $productId,
                        ],
                        [
                            'total_pieces' => 0,
                            'quantity' => 0,
                            'price' => $sourceStock->price ?? 0,
                        ]
                    );
                    $destAvail = (float)($destStock->total_pieces > 0 ? $destStock->total_pieces : ($destStock->quantity * $ppb));
                    $destStock->total_pieces = $destAvail + $totalQty;
                    $destStock->quantity = round($destStock->total_pieces / $ppb, 2);
                    $destStock->save();

                    // Record Transfer
                    $transfer = StockTransfer::create([
                        'from_warehouse_id' => $fromWarehouse,
                        'to_warehouse_id' => $destWhId,
                        'to_shop' => $toShop,
                        'product_id' => $productId,
                        'quantity' => $totalQty,
                        'remarks' => $remarks,
                    ]);

                    // Log Movement (OUT)
                    \Illuminate\Support\Facades\DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'type' => 'out',
                        'qty' => -1 * abs($totalQty),
                        'ref_type' => 'TRANSFER_OUT',
                        'ref_id' => $transfer->id,
                        'note' => 'Transfer to '.($toShop ? 'Main Shop' : "Warehouse #{$destWhId}"),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Log Movement (IN)
                    \Illuminate\Support\Facades\DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'type' => 'in',
                        'qty' => abs($totalQty),
                        'ref_type' => 'TRANSFER_IN',
                        'ref_id' => $transfer->id,
                        'note' => "Transfer from Warehouse #{$fromWarehouse}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Transfer Failed: '.$e->getMessage());
        }

        return redirect()->route('stock_transfers.index')->with('success', 'Stock transferred successfully.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        // Optional: reverse the transfer if needed
        return back()->with('error', 'Deleting transfers not allowed.');
    }

    public function getStockQuantity(Request $request)
    {
        $stock = WarehouseStock::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->first();

        $qty = $stock ? ($stock->total_pieces > 0 ? $stock->total_pieces : $stock->quantity) : 0;

        return response()->json([
            'quantity' => $qty,
        ]);
    }

    public function getProductsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        if (! $warehouseId) {
            return response()->json([]);
        }

        $products = Product::join('warehouse_stocks', 'products.id', '=', 'warehouse_stocks.product_id')
            ->where('warehouse_stocks.warehouse_id', $warehouseId)
            ->where(function($q) {
                $q->where('warehouse_stocks.total_pieces', '>', 0)
                  ->orWhere('warehouse_stocks.quantity', '>', 0);
            })
            ->select('products.id', 'products.item_name')
            ->distinct()
            ->get();

        return response()->json($products);
    }
}

// delvivery challan
// convet out per  stock ledger maintain
