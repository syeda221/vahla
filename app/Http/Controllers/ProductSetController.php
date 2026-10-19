<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSet;
use App\Models\ProductSetItem;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSetController extends Controller
{
    /**
     * Display a listing of product sets.
     */
    public function index(Request $request)
    {
        $sets = ProductSet::with(['items.product.brand', 'items.product.unit'])
            ->withCount('items')
            ->orderBy('id', 'desc')
            ->get();

        $allProducts = Product::where('is_active', true)
            ->select('id', 'item_name', 'item_code', 'sale_price_per_piece', 'sale_price_per_box', 'size_mode', 'pieces_per_box', 'unit_id', 'color', 'weight_per_piece')
            ->with('unit')
            ->orderBy('item_name')
            ->get();

        return view('admin_panel.product.sets.index', compact('sets', 'allProducts'));
    }

    /**
     * Store a newly created product set in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            $isEdit = $request->filled('set_id');
            $set = $isEdit ? ProductSet::findOrFail($request->set_id) : new ProductSet();

            $set->name = $request->name;
            $set->code = $request->code ?: null;
            $set->description = $request->description ?: null;
            $set->set_price = $request->filled('set_price') ? (float)$request->set_price : null;
            $set->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
            if (!$isEdit) {
                $set->created_by = auth()->id();
            }
            $set->save();

            // Re-sync items
            if ($isEdit) {
                ProductSetItem::where('product_set_id', $set->id)->delete();
            }

            $productIds = $request->product_id;
            $quantities = $request->quantity;

            foreach ($productIds as $idx => $pid) {
                $qty = (float)($quantities[$idx] ?? 1);
                if ($pid && $qty > 0) {
                    $rawVData = $request->variant_data[$idx] ?? null;
                    $vData = null;
                    if (!empty($rawVData)) {
                        $b64 = base64_decode($rawVData, true);
                        $vData = ($b64 !== false) ? json_decode($b64, true) : json_decode($rawVData, true);
                        if (!is_array($vData)) $vData = null;
                    }

                    $vName = $request->variant_name[$idx] ?? null;
                    if ($vData && !empty($vData['name'])) {
                        $vName = $vData['name'];
                    }

                    $vUnit = $request->variant_unit[$idx] ?? null;
                    if ($vData && !empty($vData['unit'])) {
                        $vUnit = $vData['unit'];
                    }

                    ProductSetItem::create([
                        'product_set_id'   => $set->id,
                        'product_id'       => $pid,
                        'quantity'         => $qty,
                        'variant_key'      => $request->variant_key[$idx] ?? null,
                        'variant_unit'     => $vUnit,
                        'variant_name'     => $vName,
                        'weight_per_piece' => !empty($request->weight_per_piece[$idx]) ? (float)$request->weight_per_piece[$idx] : null,
                        'conv_factor'      => !empty($request->conv_factor[$idx]) ? (float)$request->conv_factor[$idx] : null,
                        'sale_price'       => !empty($request->sale_price[$idx]) ? (float)$request->sale_price[$idx] : null,
                        'variant_data'     => $rawVData,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product Set ' . ($isEdit ? 'updated' : 'created') . ' successfully!',
                    'set' => $set->load('items.product')
                ]);
            }

            return redirect()->route('product-sets.index')
                ->with('success', 'Product Set ' . ($isEdit ? 'updated' : 'created') . ' successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Error saving set: ' . $e->getMessage());
        }
    }

    /**
     * Get details of a single product set (for editing or view).
     */
    public function show($id)
    {
        $set = ProductSet::with(['items.product'])->findOrFail($id);
        return response()->json($set);
    }

    /**
     * Remove the specified product set from storage.
     */
    public function destroy($id)
    {
        try {
            $set = ProductSet::findOrFail($id);
            $set->items()->delete();
            $set->delete();

            return redirect()->route('product-sets.index')
                ->with('success', 'Product Set deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting set: ' . $e->getMessage());
        }
    }

    /**
     * API: Check if a product belongs to any Product Set.
     * Used by the Sales Screen when a product is selected.
     */
    public function checkProductSet(Request $request, $productId)
    {
        $warehouseId = (int)$request->input('warehouse_id', auth()->user()->warehouse_id ?? 1);

        // Find active sets that contain this product
        $setIds = ProductSetItem::where('product_id', $productId)
            ->pluck('product_set_id')
            ->unique();

        if ($setIds->isEmpty()) {
            return response()->json(['has_set' => false]);
        }

        $sets = ProductSet::whereIn('id', $setIds)
            ->where('is_active', true)
            ->with(['items.product.unit', 'items.product.brand'])
            ->get();

        if ($sets->isEmpty()) {
            return response()->json(['has_set' => false]);
        }

        $formattedSets = [];

        foreach ($sets as $set) {
            $itemsList = [];
            foreach ($set->items as $item) {
                $p = $item->product;
                if (!$p) continue;

                // Fetch stock in current warehouse
                $stockRecord = WarehouseStock::where('product_id', $p->id)
                    ->where('warehouse_id', $warehouseId)
                    ->first();
                $stockPieces = (float)($stockRecord->total_pieces ?? 0);

                $vData = null;
                if (!empty($item->variant_data)) {
                    $b64 = base64_decode($item->variant_data, true);
                    $vData = ($b64 !== false) ? json_decode($b64, true) : json_decode($item->variant_data, true);
                    if (!is_array($vData)) $vData = null;
                }

                $variantName = !empty($vData['name']) ? $vData['name'] : ($item->variant_name ?: $p->item_name);
                $vUnit = !empty($vData['unit']) ? $vData['unit'] : ($item->variant_unit ?: ($p->unit->name ?? 'Pcs'));
                $convFactor = (float)(isset($vData['conv_factor']) && (float)$vData['conv_factor'] > 0 ? $vData['conv_factor'] : ($item->conv_factor ?? 1));
                $weightPerPiece = (float)(isset($vData['weight_per_piece']) && $vData['weight_per_piece'] !== '' ? $vData['weight_per_piece'] : ($item->weight_per_piece ?? 0));
                
                $ppb = (float)($p->pieces_per_box > 0 ? $p->pieces_per_box : 1);
                $rate = (float)($item->sale_price > 0 ? $item->sale_price : (!empty($vData['sale_price']) ? $vData['sale_price'] : ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : ($p->sale_price_per_box ?? 0))));

                $variantStockPieces = $stockPieces;
                $vStockDisplay = (string)$stockPieces;

                if ($p->size_mode === 'by_kg') {
                    if ($convFactor > 0) {
                        if ($convFactor == 1) {
                            $vBalance = $stockPieces;
                            if ($vBalance < 0.001 && $vBalance >= 0) {
                                $vStockDisplay = "0 {$vUnit}";
                            } elseif ($vBalance < 1 && $vBalance > 0) {
                                $gmVal = (int) round($vBalance * 1000);
                                $vStockDisplay = "{$vBalance} {$vUnit} ({$gmVal} Gm)";
                            } else {
                                $vStockDisplay = "{$vBalance} {$vUnit}";
                            }
                            $variantStockPieces = $vBalance;
                        } else {
                            $pcsCount = (int) floor($stockPieces / $convFactor);
                            $vStockDisplay = "{$pcsCount}";
                            $variantStockPieces = $pcsCount;
                        }
                    }
                } elseif (($p->size_mode === 'by_cartons' || $p->size_mode === 'by_size') && $ppb > 1) {
                    $vBoxes = (int) floor($stockPieces / $ppb);
                    $vLoose = (int) round($stockPieces - ($vBoxes * $ppb));
                    $vStockDisplay = $vLoose > 0 ? "{$vBoxes}.{$vLoose}" : (string)$vBoxes;
                    $variantStockPieces = $stockPieces;
                } elseif ($vData && isset($vData['stock'])) {
                    if (!$stockRecord && (float)$vData['stock'] > 0) {
                        $variantStockPieces = (float)$vData['stock'];
                        $vStockDisplay = (string)$variantStockPieces;
                    }
                }

                if ($vData) {
                    $vData['current_stock'] = $vStockDisplay;
                    $variantDataEncoded = base64_encode(json_encode($vData));
                } else {
                    $variantDataEncoded = $item->variant_data;
                }

                $itemsList[] = [
                    'product_id' => $p->id,
                    'item_name' => $p->item_name,
                    'item_code' => $p->item_code,
                    'set_qty' => (float)$item->quantity,
                    'pieces_per_box' => $ppb,
                    'size_mode' => $p->size_mode ?: 'by_pieces',
                    'unit_name' => $vUnit,
                    'brand_name' => $p->brand->name ?? '',
                    'retail_price' => $rate,
                    'wholesale_price' => (float)($p->wholesale_price ?? 0),
                    'sale_price_per_piece' => (float)($p->sale_price_per_piece ?? $rate),
                    'sale_price_per_box' => (float)($p->sale_price_per_box ?? 0),
                    'stock' => $vStockDisplay,
                    'stock_pieces' => $variantStockPieces,
                    'color' => $p->color,
                    'height' => $p->height ?: '-',
                    'width' => $p->width ?: '-',
                    // Variant & Weight attributes
                    'variant_key' => $item->variant_key,
                    'variant_unit' => $vUnit,
                    'variant_name' => $variantName,
                    'weight_per_piece' => $weightPerPiece,
                    'conv_factor' => $convFactor,
                    'variant_data' => $variantDataEncoded,
                ];
            }

            $formattedSets[] = [
                'id' => $set->id,
                'name' => $set->name,
                'code' => $set->code,
                'description' => $set->description,
                'set_price' => $set->set_price,
                'items_count' => count($itemsList),
                'items' => $itemsList,
            ];
        }

        return response()->json([
            'has_set' => true,
            'sets' => $formattedSets,
            'primary_set' => $formattedSets[0] ?? null,
        ]);
    }
}
