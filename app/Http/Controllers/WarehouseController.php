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

        $product = \App\Models\Product::with('unit')->find($productId);
        $ppb = ($product && $product->pieces_per_box > 0) ? (float) $product->pieces_per_box : 1.0;
        $sizeMode = $product ? $product->size_mode : 'std';
        $wpp = ($product && $product->weight_per_piece > 0) ? (float) $product->weight_per_piece : 0.0;
        $unitName = $product && $product->unit ? $product->unit->name : '';

        // Get all warehouses first
        $allWarehouses = Warehouse::all();
        
        // Sum stock entries for this product grouped by warehouse_id (handles multiple rows per warehouse cleanly)
        $stockSums = \Illuminate\Support\Facades\DB::table('warehouse_stocks')
            ->where('product_id', $productId)
            ->groupBy('warehouse_id')
            ->select(
                'warehouse_id',
                \Illuminate\Support\Facades\DB::raw('SUM(total_pieces) as total_pieces'),
                \Illuminate\Support\Facades\DB::raw('SUM(quantity) as quantity')
            )
            ->get()
            ->keyBy('warehouse_id');

        $response = $allWarehouses->map(function ($warehouse) use ($stockSums, $ppb, $sizeMode, $wpp, $unitName) {
            $ws = $stockSums->get($warehouse->id);
            $stockVal = 0.0;
            
            if ($ws) {
                $totalPieces = (float) $ws->total_pieces;
                $qty = (float) $ws->quantity;
                if ($totalPieces != 0.0) {
                    $stockVal = $totalPieces;
                } else {
                    $stockVal = $qty * $ppb;
                }
            }

            $cleanStock = (float) $stockVal;
            $cleanStock = ($cleanStock == (int) $cleanStock) ? (int) $cleanStock : round($cleanStock, 4);

            return [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->warehouse_name,
                'is_shop' => (int) ($warehouse->is_shop ?? 0),
                'stock' => $cleanStock, // Total pieces/kg summed across all records
                'boxes' => $ppb > 0 ? ($stockVal / $ppb) : 0, // Dynamically calculated box quantity
                'ppb' => $ppb,
                'size_mode' => $sizeMode,
                'weight_per_piece' => $wpp,
                'unit_name' => $unitName,
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
