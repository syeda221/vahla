<?php

namespace App\Http\Controllers;

use App\Models\ItemCodeSeries;
use App\Models\Product;
use App\Models\ProductSet;
use App\Models\ProductSetItem;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSetController extends Controller
{
    /**
     * Get the next auto-incrementing code for the SET- series.
     */
    public static function getNextSetCode()
    {
        $series = ItemCodeSeries::firstOrCreate(['prefix' => 'SET-'], ['next_number' => 1]);
        $nextNum = (int)$series->next_number;
        do {
            $code = 'SET-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (ProductSet::where('code', $code)->exists());

        return $code;
    }

    /**
     * API: Get the next auto SET- code.
     */
    public function nextCode()
    {
        return response()->json([
            'next_code' => self::getNextSetCode(),
        ]);
    }

    /**
     * Display a listing of product sets.
     */
    public function index(Request $request)
    {
        $sets = ProductSet::with(['items.product.brand', 'items.product.unit'])
            ->withCount('items')
            ->orderBy('id', 'desc')
            ->get();

        $nextSetCode = self::getNextSetCode();

        return view('admin_panel.product.sets.index', compact('sets', 'nextSetCode'));
    }

    /**
     * AJAX Search for Products and Variants for Product Set creation / editing.
     * High performance, paginated, with instant multi-attribute matching.
     */
    public function ajaxProducts(Request $request)
    {
        $term = trim($request->input('q') ?? $request->input('term') ?? '');
        $page = max(1, (int)$request->input('page', 1));
        $perPage = 25;

        $query = Product::where('is_active', true)
            ->select('id', 'item_name', 'item_code', 'barcode_path', 'sale_price_per_piece', 'sale_price_per_box', 'size_mode', 'pieces_per_box', 'unit_id', 'color', 'weight_per_piece')
            ->with('unit');

        $words = [];
        if ($term !== '') {
            $words = array_filter(explode(' ', $term));
            $cleanTerm = preg_replace('/[^a-zA-Z0-9]/', '', $term);
            $numTerm = preg_replace('/\D/', '', $term);
            $unpaddedNum = ltrim($numTerm, '0');

            $query->where(function ($q) use ($words, $term, $cleanTerm, $numTerm, $unpaddedNum) {
                // Space-separated words search across name, code, variants
                if (!empty($words)) {
                    $q->where(function ($subQ) use ($words) {
                        foreach ($words as $w) {
                            $subQ->where(function ($wQ) use ($w) {
                                $wQ->where('item_name', 'like', "%{$w}%")
                                   ->orWhere('item_code', 'like', "%{$w}%")
                                   ->orWhere('color', 'like', "%{$w}%");
                            });
                        }
                    });
                }

                // Direct code / barcode match
                $q->orWhere('item_code', 'like', "%{$term}%")
                  ->orWhere('barcode_path', 'like', "%{$term}%");

                // Clean alphanumeric match
                if ($cleanTerm !== '') {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(item_code, '-', ''), ' ', ''), '_', '') LIKE ?", ["%{$cleanTerm}%"]);
                }

                // Number only match (e.g. '1616', '24' for 'ITEM-0024')
                if ($unpaddedNum !== '') {
                    $q->orWhere('item_code', 'like', "%{$unpaddedNum}%");
                }
            });
        }

        $query->orderBy('item_code', 'asc')->orderBy('item_name', 'asc');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $results = [];

        foreach ($paginator->items() as $p) {
            $variants = [];
            if (!empty($p->color)) {
                $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                if (is_string($parsed)) {
                    $parsed = json_decode($parsed, true);
                }
                if (is_array($parsed) && count($parsed) > 0) {
                    $variants = $parsed;
                }
            }

            $code = (string)($p->item_code ?? '');
            $codePrefix = $code !== '' ? "[{$code}] " : '';

            if (!empty($variants)) {
                foreach ($variants as $vIdx => $v) {
                    $vKey = (string)($v['barcode'] ?? $v['name'] ?? $vIdx);
                    $vUnit = $v['unit'] ?? ($p->unit->name ?? 'Pcs');
                    $vName = $v['name'] ?? $p->item_name;
                    $vBarcode = (string)($v['barcode'] ?? '');

                    // Filter variant if a specific query was provided
                    if ($term !== '') {
                        $pNameMatch = stripos($p->item_name, $term) !== false;
                        $pCodeMatch = stripos($code, $term) !== false;
                        $vNameMatch = stripos($vName, $term) !== false;
                        $vUnitMatch = stripos($vUnit, $term) !== false;
                        $vBarcodeMatch = $vBarcode !== '' && stripos($vBarcode, $term) !== false;

                        if (!$pNameMatch && !$pCodeMatch && !$vNameMatch && !$vUnitMatch && !$vBarcodeMatch) {
                            $anyWordMatch = false;
                            foreach ($words as $w) {
                                if (stripos($vName, $w) !== false || stripos($vUnit, $w) !== false || stripos($vBarcode, $w) !== false) {
                                    $anyWordMatch = true;
                                    break;
                                }
                            }
                            if (!$anyWordMatch) {
                                continue;
                            }
                        }
                    }

                    $vWeight = (isset($v['weight_per_piece']) && $v['weight_per_piece'] !== '' && $v['weight_per_piece'] !== null)
                        ? (float)$v['weight_per_piece']
                        : (float)($p->weight_per_piece ?? 0);
                    $vPrice = (float)($v['sale_price'] ?? $p->sale_price_per_piece ?? 0);
                    $vConv = (float)($v['conv_factor'] ?? 1);

                    $vDataEncoded = base64_encode(json_encode($v));

                    $weightLabel = $vWeight > 0 ? " | {$vWeight}g" : '';
                    $priceLabel = $vPrice > 0 ? " | Rs. " . number_format($vPrice, 2) : '';
                    $displayText = "{$codePrefix}{$vName} [{$vUnit}{$weightLabel}{$priceLabel}]";

                    $results[] = [
                        'id'       => "{$p->id}||{$vIdx}",
                        'pid'      => $p->id,
                        'vidx'     => $vIdx,
                        'code'     => $code,
                        'vkey'     => $vKey,
                        'vunit'    => $vUnit,
                        'vname'    => $vName,
                        'vweight'  => $vWeight,
                        'vconv'    => $vConv,
                        'vprice'   => $vPrice,
                        'vdata'    => $vDataEncoded,
                        'text'     => $displayText,
                    ];
                }
            } else {
                $stdUnit = $p->unit->name ?? ($p->size_mode === 'by_cartons' ? 'Carton' : 'Pcs');
                $stdPrice = (float)($p->sale_price_per_piece ?: ($p->sale_price_per_box ?? 0));
                $stdWeight = (float)($p->weight_per_piece ?? 0);

                $weightLabel = $stdWeight > 0 ? " | {$stdWeight}g" : '';
                $priceLabel = $stdPrice > 0 ? " | Rs. " . number_format($stdPrice, 2) : '';
                $displayText = "{$codePrefix}{$p->item_name} [{$stdUnit}{$weightLabel}{$priceLabel}]";

                $results[] = [
                    'id'       => "{$p->id}||base",
                    'pid'      => $p->id,
                    'vidx'     => 'base',
                    'code'     => $code,
                    'vkey'     => 'base',
                    'vunit'    => $stdUnit,
                    'vname'    => $p->item_name,
                    'vweight'  => $stdWeight,
                    'vconv'    => 1,
                    'vprice'   => $stdPrice,
                    'vdata'    => '',
                    'text'     => $displayText,
                ];
            }
        }

        return response()->json([
            'results'    => $results,
            'pagination' => [
                'more' => $paginator->hasMorePages(),
            ],
        ]);
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

            $code = trim($request->input('code') ?? '');
            if (!$code && !$isEdit) {
                $code = self::getNextSetCode();
            }

            if ($code) {
                $existsQuery = ProductSet::where('code', $code);
                if ($isEdit) {
                    $existsQuery->where('id', '!=', $set->id);
                }
                if ($existsQuery->exists()) {
                    throw new \Exception("Set code '{$code}' already exists. Please choose a different code.");
                }
            }

            $set->name = $request->name;
            $set->code = $code ?: null;
            $set->description = $request->description ?: null;
            $set->set_price = $request->filled('set_price') && (float)$request->set_price > 0 ? (float)$request->set_price : null;
            $set->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
            if (!$isEdit) {
                $set->created_by = auth()->id();
            }
            $set->save();

            // Update SET- series next number if applicable
            if ($code && str_starts_with($code, 'SET-')) {
                $numPart = str_replace('SET-', '', $code);
                if (is_numeric($numPart)) {
                    $series = ItemCodeSeries::firstOrCreate(['prefix' => 'SET-'], ['next_number' => 1]);
                    if ((int)$numPart >= $series->next_number) {
                        $series->next_number = (int)$numPart + 1;
                        $series->save();
                    }
                }
            }

            // Re-sync items
            if ($isEdit) {
                ProductSetItem::where('product_set_id', $set->id)->delete();
            }

            $productIds = $request->product_id;
            $quantities = $request->quantity;
            $calculatedSetPrice = 0;

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

                    $rowPrice = !empty($request->sale_price[$idx]) ? (float)$request->sale_price[$idx] : 0;
                    $calculatedSetPrice += ($qty * $rowPrice);

                    ProductSetItem::create([
                        'product_set_id'   => $set->id,
                        'product_id'       => $pid,
                        'quantity'         => $qty,
                        'variant_key'      => $request->variant_key[$idx] ?? null,
                        'variant_unit'     => $vUnit,
                        'variant_name'     => $vName,
                        'weight_per_piece' => !empty($request->weight_per_piece[$idx]) ? (float)$request->weight_per_piece[$idx] : null,
                        'conv_factor'      => !empty($request->conv_factor[$idx]) ? (float)$request->conv_factor[$idx] : null,
                        'sale_price'       => $rowPrice > 0 ? $rowPrice : null,
                        'variant_data'     => $rawVData,
                    ]);
                }
            }

            if ((!$request->filled('set_price') || (float)$request->set_price <= 0) && $calculatedSetPrice > 0) {
                $set->set_price = $calculatedSetPrice;
                $set->save();
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
        $set = ProductSet::with(['items.product.unit'])->findOrFail($id);
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
