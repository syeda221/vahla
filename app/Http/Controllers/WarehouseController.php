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

        // Get all warehouses first
        $allWarehouses = Warehouse::all();
        
        // Get stock entries for this product
        $warehouseStocks = WarehouseStock::with(['stockWarehouse', 'product'])
            ->where('product_id', $productId)
            ->get()
            ->keyBy('warehouse_id');

        $response = $allWarehouses->map(function ($warehouse) use ($warehouseStocks, $productId) {
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

            return [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->warehouse_name,
                'stock' => $stockVal, // Total pieces
                'boxes' => $ws ? ($stockVal / $ppb) : 0, // Dynamically calculated box quantity to prevent rounding errors
                'ppb' => $ws && $ws->product ? $ws->product->pieces_per_box : 1,
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
        $warehouses = Warehouse::with('user')
            ->withCount('warehouseStocks')
            ->withSum('warehouseStocks', 'total_pieces')
            ->orderByRaw('is_shop DESC, warehouse_name ASC')
            ->get();

        return view('admin_panel.warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'location'       => 'nullable|string|max:255',
            'remarks'        => 'nullable|string|max:500',
            'is_shop'        => 'nullable',
        ]);

        $data = [
            'warehouse_name' => $validated['warehouse_name'],
            'location'       => $validated['location'] ?? null,
            'remarks'        => $validated['remarks'] ?? null,
            'is_shop'        => (int) ($request->input('is_shop', 0)),
        ];

        if ($request->id) {
            if (! auth()->user()->can('warehouse.edit')) {
                return back()->with('error', 'Unauthorized action.');
            }
            Warehouse::findOrFail($request->id)->update($data);

            return back()->with('success', 'Warehouse / Shop Updated Successfully');
        } else {
            if (! auth()->user()->can('warehouse.create')) {
                return back()->with('error', 'Unauthorized action.');
            }
            $data['creater_id'] = auth()->id();
            Warehouse::create($data);

            return back()->with('success', 'Warehouse / Shop Created Successfully');
        }
    }

    public function delete($id)
    {
        if (! auth()->user()->can('warehouse.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        $wh = Warehouse::findOrFail($id);
        if ($wh->id == 1) {
            return response()->json(['error' => 'Main Store / Shop cannot be deleted.'], 422);
        }
        $hasStock = $wh->warehouseStocks()->where('total_pieces', '!=', 0)->exists();
        if ($hasStock) {
            return response()->json(['error' => 'Cannot delete warehouse with existing active stock inventory.'], 422);
        }
        $wh->delete();

        return response()->json([
            'success' => 'Warehouse Deleted Successfully',
            'reload' => true,
        ]);
    }
}
