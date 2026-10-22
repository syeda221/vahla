<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // Return warehouses for a given product_id
    public function getWarehouses(Request $request)
    {
        $productId = $request->input('product_id');
        $variantName = $request->input('variant_name');

        // Get all warehouses first
        $allWarehouses = Warehouse::all();
        
        // Get stock entries for this product
        $warehouseStocks = WarehouseStock::with(['stockWarehouse', 'product'])
            ->where('product_id', $productId)
            ->get()
            ->keyBy('warehouse_id');

        $opStocks = \App\Models\OpeningStock::where('product_id', $productId)->get();
        $hasAnyOpStocks = $opStocks->isNotEmpty();

        $response = $allWarehouses->map(function ($warehouse) use ($warehouseStocks, $productId, $variantName, $hasAnyOpStocks, $opStocks) {
            $ws = $warehouseStocks->get($warehouse->id);
            $stockVal = 0;
            
            if ($ws) {
                $ppb = ($ws->product && $ws->product->pieces_per_box > 0) ? $ws->product->pieces_per_box : 1;
                
                // Trust total_pieces as the absolute source of truth, fallback to quantity * ppb only if total_pieces is 0
                if ($ws->total_pieces != 0) {
                     $stockVal = $ws->total_pieces;
                } else {
                     $stockVal = $ws->quantity * $ppb;
                }
            }

            if (!empty($variantName) && $hasAnyOpStocks) {
                $opStock = $opStocks->where('warehouse_id', $warehouse->id)->first(function($op) use ($variantName) {
                    return ($op->variant_name === $variantName) || empty($op->variant_name);
                });
                if ($opStock) {
                    $vSales = \Illuminate\Support\Facades\DB::table('sale_items')
                        ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                        ->where('sale_items.warehouse_id', $warehouse->id)
                        ->where('sale_items.product_id', $productId)
                        ->whereIn('sales.sale_status', ['posted', 'returned'])
                        ->get()
                        ->filter(function($si) use ($variantName) {
                            $raw = $si->color;
                            $b64 = base64_decode($raw, true);
                            if ($b64) $raw = $b64;
                            $arr = json_decode($raw, true);
                            return ($arr['name'] ?? null) === $variantName;
                        })
                        ->sum('total_pieces');
                    $stockVal = max(0, (float)$opStock->total_pieces - $vSales);
                } else {
                    $stockVal = 0;
                }
            }

            $ppb = ($ws && $ws->product && $ws->product->pieces_per_box > 0) ? $ws->product->pieces_per_box : 1;

            return [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->warehouse_name,
                'stock' => $stockVal, // Total pieces
                'boxes' => $ppb > 0 ? ($stockVal / $ppb) : 0, // Dynamically calculated box quantity to prevent rounding errors
                'ppb' => $ppb,
                'size_mode' => $ws && $ws->product ? $ws->product->size_mode : 'std',
            ];
        });

        return response()->json($response);
    }

    // VendorController.php aur WarehouseController.php same hoga
    public function index()
    {
        if (! auth()->user()->can('warehouse.view')) {
            abort(403, 'Unauthorized action.');
        }
        $warehouses = Warehouse::with('user')->get(); // ya $warehouses = Warehouse::all();

        return view('admin_panel.warehouses.index', compact('warehouses')); // ya warehouses.index
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token']);
        if (empty($data['creater_id'])) {
            $data['creater_id'] = auth()->id();
        }

        if ($request->filled('id')) {
            if (! auth()->user()->can('warehouse.edit')) {
                return back()->with('error', 'Unauthorized action.');
            }
            $warehouseId = $request->id;
            unset($data['id']);
            Warehouse::findOrFail($warehouseId)->update($data);

            return back()->with('success', 'Warehouse Updated Successfully');
        } else {
            if (! auth()->user()->can('warehouse.create')) {
                return back()->with('error', 'Unauthorized action.');
            }
            unset($data['id']);
            Warehouse::create($data);

            return back()->with('success', 'Warehouse Created Successfully');
        }
    }

    public function delete($id)
    {
        if (! auth()->user()->can('warehouse.delete')) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            return redirect('/warehouse')->with('error', 'Unauthorized action.');
        }

        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            $warehouse->delete();
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => 'Warehouse Deleted Successfully',
                'reload' => true,
            ]);
        }

        return redirect('/warehouse')->with('success', 'Warehouse Deleted Successfully');
    }
}
