<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\ItemCodeSeries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductPricingController extends Controller
{
    /**
     * Display the Product Pricing & Stock Valuation spreadsheet interface.
     */
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        $units      = Unit::orderBy('name')->get();

        $query = Product::with([
            'warehouseStocks',
            'unit',
            'category_relation',
            'sub_category_relation',
            'brand',
        ]);

        // Search Filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('item_name', 'like', "%{$s}%")
                  ->orWhere('item_code', 'like', "%{$s}%")
                  ->orWhere('barcode_path', 'like', "%{$s}%")
                  ->orWhere('color', 'like', "%{$s}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Brand Filter
        if ($request->filled('brand_id') && $request->brand_id !== 'all') {
            $query->where('brand_id', $request->brand_id);
        }

        // Size Mode Filter (Weight, Cartons, Area, Standard)
        if ($request->filled('size_mode') && $request->size_mode !== 'all') {
            if ($request->size_mode === 'by_kg') {
                $query->whereIn('size_mode', ['by_kg', 'by_gm', 'by_ton']);
            } elseif ($request->size_mode === 'by_cartons') {
                $query->whereIn('size_mode', ['by_cartons', 'by_box']);
            } elseif ($request->size_mode === 'by_size') {
                $query->whereIn('size_mode', ['by_size', 'by_m2']);
            } elseif ($request->size_mode === 'std') {
                $query->where(function ($q) {
                    $q->whereNull('size_mode')
                      ->orWhereIn('size_mode', ['std', 'by_pieces', '']);
                });
            }
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 50);
        if (!in_array($perPage, [25, 50, 100, 250, 500])) {
            $perPage = 50;
        }

        $paginator = $query->orderByRaw("CAST(SUBSTRING_INDEX(item_code, '-', -1) AS UNSIGNED) ASC, item_code ASC")
            ->paginate($perPage)
            ->withQueryString();

        // Process Products for View with Exact Item Stock Report Weighted Average Price Logic
        $items = collect($paginator->items())->map(function ($product) {
            $stockPieces = (float) $product->warehouseStocks->sum('total_pieces');
            $ppb = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
            if ($ppb <= 0) $ppb = 1;

            $unitName = $product->unit->name ?? match ($product->size_mode) {
                'by_kg' => 'Kg',
                'by_gm' => 'Gm',
                'by_ton' => 'Ton',
                'by_meter' => 'Mtr',
                'by_feet' => 'Ft',
                'by_cartons' => 'Carton',
                'by_box' => 'Box',
                'by_size' => 'M²',
                default => 'Pcs',
            };

            // Base Purchase Price & Base Sale Price
            if ($product->size_mode === 'by_size') {
                $basePurchPrice = (float) ($product->purchase_price_per_m2 ?? 0);
                $baseSalePrice  = (float) ($product->price_per_m2 ?? 0);
            } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                $basePurchPrice = (float) ($product->purchase_price_per_box ?? 0);
                $baseSalePrice  = (float) ($product->sale_price_per_box ?? 0);
            } else {
                $basePurchPrice = (float) ($product->purchase_price_per_piece ?? 0);
                $baseSalePrice  = (float) ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0);
            }

            // Weighted Average Purchase Price & Stock Valuation matching Item Stock Report
            $costData = self::calculateProductAverageCost($product);
            $avgPurchPrice = $costData['avg_purchase_price'];
            $stockValue    = $costData['stock_value'];

            // Alert Stock
            $alertQty = (float) ($product->alert_carton_quantity ?: $product->alert_quantity ?: 0);
            $isLowStock = ($alertQty > 0 && $stockPieces <= $alertQty);
            $potentialSaleValue = $stockPieces * $baseSalePrice;

            // Margin %
            $marginPct = $baseSalePrice > 0 ? ((($baseSalePrice - $avgPurchPrice) / $baseSalePrice) * 100) : 0;

            // Variants parsing
            $variants = [];
            if (!empty($product->color)) {
                $rawC = $product->color;
                $decoded = is_string($rawC) ? json_decode($rawC, true) : $rawC;
                if (is_string($decoded)) $decoded = json_decode($decoded, true);
                if (is_array($decoded)) {
                    $variants = isset($decoded['name']) || isset($decoded['color'])
                        ? (isset($decoded[0]) ? $decoded : [$decoded])
                        : array_values($decoded);
                }
            }

            // Sync with base variant if present
            $baseVar = collect($variants)->first(fn($v) => !empty($v['is_base_variant']) || (isset($v['conv_factor']) && (float)$v['conv_factor'] == 1.0));
            if (!$baseVar && count($variants) > 0) {
                $baseVar = $variants[0];
            }
            if ($baseVar) {
                if (isset($baseVar['sale_price']) && (float)$baseVar['sale_price'] > 0) {
                    $baseSalePrice = (float)$baseVar['sale_price'];
                }
                if (isset($baseVar['purch_price']) && (float)$baseVar['purch_price'] > 0) {
                    $basePurchPrice = (float)$baseVar['purch_price'];
                }
            }

            return (object) [
                'id'                   => $product->id,
                'item_code'            => $product->item_code,
                'item_name'            => $product->item_name,
                'barcode'              => $product->barcode_path,
                'size_mode'            => $product->size_mode ?: 'std',
                'unit_name'            => $unitName,
                'pieces_per_box'       => $ppb,
                'pieces_per_m2'        => (float) ($product->pieces_per_m2 ?? 0),
                'category_name'        => $product->category_relation->name ?? '-',
                'subcategory_name'     => $product->sub_category_relation->name ?? '-',
                'brand_name'           => $product->brand->name ?? '-',
                'current_stock'        => $stockPieces,
                'alert_quantity'       => $alertQty,
                'is_low_stock'         => $isLowStock,
                'avg_purchase_price'   => $avgPurchPrice,
                'base_purchase_price'  => $basePurchPrice,
                'base_sale_price'      => $baseSalePrice,
                'wholesale_price'      => (float) ($product->wholesale_price ?? 0),
                'stock_value'          => $stockValue,
                'potential_sale_value' => $potentialSaleValue,
                'margin_pct'           => $marginPct,
                'variants'             => $variants,
                'is_active'            => (bool) $product->is_active,
            ];
        });

        // Stock Status Filter on in-memory collection if selected
        if ($request->filled('stock_status') && $request->stock_status !== 'all') {
            if ($request->stock_status === 'in_stock') {
                $items = $items->filter(fn($i) => $i->current_stock > 0);
            } elseif ($request->stock_status === 'low_stock') {
                $items = $items->filter(fn($i) => $i->is_low_stock);
            } elseif ($request->stock_status === 'out_of_stock') {
                $items = $items->filter(fn($i) => $i->current_stock <= 0);
            }
        }

        // Top Executive KPI Calculations
        $totalStockValue   = $items->sum('stock_value');
        $totalSaleValue    = $items->sum('potential_sale_value');
        $totalStockPieces  = $items->sum('current_stock');
        $lowStockCount     = $items->where('is_low_stock', true)->count();
        $totalProducts     = $paginator->total();

        // Load Item Code Series for quick editing & numbering
        $itemCodeSeries = ItemCodeSeries::orderBy('is_default', 'desc')->orderBy('prefix')->get();
        if ($itemCodeSeries->isEmpty()) {
            ItemCodeSeries::create([
                'prefix' => 'item-',
                'next_number' => 1,
                'is_default' => 1,
            ]);
            $itemCodeSeries = ItemCodeSeries::all();
        }

        return view('admin_panel.product.pricing_stock_value', compact(
            'paginator',
            'items',
            'categories',
            'brands',
            'units',
            'itemCodeSeries',
            'totalStockValue',
            'totalSaleValue',
            'totalStockPieces',
            'lowStockCount',
            'totalProducts'
        ));
    }

    /**
     * Calculate Weighted Average Purchase Cost and Total Stock Valuation
     * Matching the exact formula of Item Stock & Movement Report (ReportingController).
     */
    public static function calculateProductAverageCost(Product $product): array
    {
        $reporting = app(ReportingController::class);
        $req = new Request();
        $req->merge([
            'product_id' => $product->id,
            'per_page'   => 50,
        ]);
        $res = $reporting->fetchItemStock($req);
        $data = json_decode($res->getContent(), true)['data'] ?? [];

        $baseRow = $data[0] ?? null;

        $basePurch = match ($product->size_mode) {
            'by_size' => (float) ($product->purchase_price_per_m2 ?? 0),
            'by_cartons', 'by_box' => (float) ($product->purchase_price_per_box ?? 0),
            default => (float) ($product->purchase_price_per_piece ?? 0),
        };

        $avgPrice = isset($baseRow['average_price']) ? (float) $baseRow['average_price'] : $basePurch;
        $stockVal = isset($baseRow['stock_value']) ? (float) $baseRow['stock_value'] : ($product->warehouseStocks->sum('total_pieces') * $avgPrice);
        $stockPieces = isset($baseRow['balance']) ? (float) $baseRow['balance'] : (float) $product->warehouseStocks->sum('total_pieces');

        return [
            'avg_purchase_price' => $avgPrice,
            'stock_value'        => $stockVal,
            'stock_pieces'       => $stockPieces,
            'report_rows'        => $data,
        ];
    }

    /**
     * AJAX Inline Update endpoint for Purchase Price, Sale Price, and Alert Quantity.
     * Automatically recalculates and synchronizes variant piece prices for Kg/Gram/Box items.
     */
    public function inlineUpdate(Request $request)
    {
        $validated = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'variant_index' => 'nullable|integer|min:0',
            'field'         => 'required|string',
            'value'         => 'required',
        ]);

        // ── Handle Item Code Update directly via inlineUpdate ──
        if ($validated['field'] === 'item_code') {
            return $this->processItemCodeUpdate(
                (int) $validated['product_id'],
                trim($validated['value']),
                trim($request->input('prefix', ''))
            );
        }

        if (!is_numeric($validated['value']) || (float)$validated['value'] < 0) {
            return response()->json([
                'success' => false,
                'message' => 'The value must be a valid non-negative number.',
            ], 422);
        }

        $product = Product::with('warehouseStocks')->findOrFail($validated['product_id']);
        $field = $validated['field'];
        $val = (float) $validated['value'];

        $ppb = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
        if ($ppb <= 0) $ppb = 1;

        $ppm2 = (float) ($product->pieces_per_m2 > 0 ? $product->pieces_per_m2 : 1);

        // Decode variants if present
        $variants = [];
        if (!empty($product->color)) {
            $rawC = $product->color;
            $decoded = is_string($rawC) ? json_decode($rawC, true) : $rawC;
            if (is_string($decoded)) $decoded = json_decode($decoded, true);
            if (is_array($decoded)) {
                $variants = isset($decoded['name']) || isset($decoded['color'])
                    ? (isset($decoded[0]) ? $decoded : [$decoded])
                    : array_values($decoded);
            }
        }

        // ── Variant-specific Rate Override ──
        if ($request->has('variant_index') && $request->input('variant_index') !== null && $request->input('variant_index') !== '') {
            $vIdx = (int) $request->input('variant_index');
            if (!isset($variants[$vIdx])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Variant index not found.',
                ], 422);
            }

            if (in_array($field, ['purchase_price', 'purch_price'])) {
                $variants[$vIdx]['purch_price'] = $val;
                $variants[$vIdx]['purchase_price'] = $val;
            } elseif ($field === 'sale_price') {
                $variants[$vIdx]['sale_price'] = $val;
                $variants[$vIdx]['retail_price'] = $val;
            } elseif ($field === 'wholesale_price') {
                $variants[$vIdx]['wholesale_price'] = $val;
            } elseif ($field === 'retail_price') {
                $variants[$vIdx]['retail_price'] = $val;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid variant field: ' . $field,
                ], 422);
            }

            // Check if this variant is the base variant
            $isBaseVariant = false;
            if (!empty($variants[$vIdx]['is_base_variant']) && $variants[$vIdx]['is_base_variant'] != '0') {
                $isBaseVariant = true;
            } elseif (isset($variants[$vIdx]['conv_factor']) && (float)$variants[$vIdx]['conv_factor'] == 1.0) {
                $isBaseVariant = true;
            } elseif (isset($variants[$vIdx]['unit']) && $product->unit && strtolower(trim($variants[$vIdx]['unit'])) === strtolower(trim($product->unit->name))) {
                $isBaseVariant = true;
            } elseif ($vIdx === 0) {
                $hasOtherBase = false;
                foreach ($variants as $k => $vCheck) {
                    if ($k !== 0 && (!empty($vCheck['is_base_variant']) || (isset($vCheck['conv_factor']) && (float)$vCheck['conv_factor'] == 1.0))) {
                        $hasOtherBase = true;
                        break;
                    }
                }
                if (!$hasOtherBase) {
                    $isBaseVariant = true;
                }
            }

            if ($isBaseVariant) {
                if ($field === 'sale_price' || $field === 'retail_price') {
                    if ($product->size_mode === 'by_size') {
                        $product->price_per_m2 = $val;
                        $product->sale_price_per_piece = round($val * $ppm2, 2);
                    } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                        $product->sale_price_per_box = $val;
                        $product->sale_price_per_piece = round($val / $ppb, 2);
                    } else {
                        $product->sale_price_per_piece = $val;
                        $product->sale_price_per_box = round($val * $ppb, 2);
                    }

                    // Recalculate other dependent variants that have conv_factor
                    foreach ($variants as $k => &$v) {
                        if ($k === $vIdx) continue;
                        if (in_array($product->size_mode, ['by_kg', 'by_meter', 'by_feet'])) {
                            $conv = (float) ($v['conv_factor'] ?? 0);
                            if ($conv <= 0 && isset($v['weight_per_piece'])) {
                                if ($product->size_mode === 'by_feet') {
                                    $conv = (float) $v['weight_per_piece'] / 12.0;
                                } else {
                                    $conv = (float) $v['weight_per_piece'] / 1000.0;
                                }
                            }
                            if ($conv > 0) {
                                $v['sale_price']   = round($val * $conv, 2);
                                $v['retail_price'] = round($val * $conv, 2);
                            }
                        } elseif (($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') && $ppb > 0) {
                            $v['sale_price']   = round($val / $ppb, 2);
                            $v['retail_price'] = round($val / $ppb, 2);
                        }
                    }
                    unset($v);
                } elseif (in_array($field, ['purchase_price', 'purch_price'])) {
                    if ($product->size_mode === 'by_size') {
                        $product->purchase_price_per_m2 = $val;
                        $product->purchase_price_per_piece = round($val * $ppm2, 2);
                    } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                        $product->purchase_price_per_box = $val;
                        $product->purchase_price_per_piece = round($val / $ppb, 2);
                    } else {
                        $product->purchase_price_per_piece = $val;
                        $product->purchase_price_per_box = round($val * $ppb, 2);
                    }

                    // Recalculate other dependent variants that have conv_factor
                    foreach ($variants as $k => &$v) {
                        if ($k === $vIdx) continue;
                        if (in_array($product->size_mode, ['by_kg', 'by_meter', 'by_feet'])) {
                            $conv = (float) ($v['conv_factor'] ?? 0);
                            if ($conv <= 0 && isset($v['weight_per_piece'])) {
                                if ($product->size_mode === 'by_feet') {
                                    $conv = (float) $v['weight_per_piece'] / 12.0;
                                } else {
                                    $conv = (float) $v['weight_per_piece'] / 1000.0;
                                }
                            }
                            if ($conv > 0) {
                                $v['purch_price']    = round($val * $conv, 2);
                                $v['purchase_price'] = round($val * $conv, 2);
                            }
                        } elseif (($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') && $ppb > 0) {
                            $v['purch_price']    = round($val / $ppb, 2);
                            $v['purchase_price'] = round($val / $ppb, 2);
                        }
                    }
                    unset($v);
                } elseif ($field === 'wholesale_price') {
                    $product->wholesale_price = $val;

                    foreach ($variants as $k => &$v) {
                        if ($k === $vIdx) continue;
                        if (in_array($product->size_mode, ['by_kg', 'by_meter', 'by_feet'])) {
                            $conv = (float) ($v['conv_factor'] ?? 0);
                            if ($conv <= 0 && isset($v['weight_per_piece'])) {
                                if ($product->size_mode === 'by_feet') {
                                    $conv = (float) $v['weight_per_piece'] / 12.0;
                                } else {
                                    $conv = (float) $v['weight_per_piece'] / 1000.0;
                                }
                            }
                            if ($conv > 0) {
                                $v['wholesale_price'] = round($val * $conv, 2);
                            }
                        }
                    }
                    unset($v);
                }
            }

            $product->color = json_encode(array_values($variants));
            $product->save();

            // Calculate updated stock value and margin matching ReportingController
            $costData = self::calculateProductAverageCost($product);
            $avgPurch = $costData['avg_purchase_price'];
            $newStockValue = $costData['stock_value'];

            // Get current base prices for UI
            if ($product->size_mode === 'by_size') {
                $basePurchPrice = (float) ($product->purchase_price_per_m2 ?? 0);
                $baseSalePrice  = (float) ($product->price_per_m2 ?? 0);
            } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                $basePurchPrice = (float) ($product->purchase_price_per_box ?? 0);
                $baseSalePrice  = (float) ($product->sale_price_per_box ?? 0);
            } else {
                $basePurchPrice = (float) ($product->purchase_price_per_piece ?? 0);
                $baseSalePrice  = (float) ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0);
            }
            $wholesalePrice = (float) ($product->wholesale_price ?? 0);
            $marginPct = $baseSalePrice > 0 ? ((($baseSalePrice - $avgPurch) / $baseSalePrice) * 100) : 0;

            return response()->json([
                'success'             => true,
                'message'             => $isBaseVariant ? 'Base rate & variants updated successfully.' : 'Variant rate updated successfully.',
                'product_id'          => $product->id,
                'variant_index'       => $vIdx,
                'is_base_variant'     => $isBaseVariant,
                'field'               => $field,
                'value'               => $val,
                'variant'             => $variants[$vIdx],
                'variants'            => $variants,
                'base_purchase_price' => $basePurchPrice,
                'base_sale_price'     => $baseSalePrice,
                'wholesale_price'     => $wholesalePrice,
                'avg_purchase_price'  => $avgPurch,
                'stock_value'         => $newStockValue,
                'margin_pct'          => round($marginPct, 1),
            ]);
        }

        // ── Master Product Update ──
        if (!in_array($field, ['purchase_price', 'sale_price', 'alert_quantity', 'wholesale_price'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product field: ' . $field,
            ], 422);
        }

        if ($field === 'purchase_price') {
            if ($product->size_mode === 'by_size') {
                $product->purchase_price_per_m2 = $val;
                $product->purchase_price_per_piece = round($val * $ppm2, 2);
            } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                $product->purchase_price_per_box = $val;
                $product->purchase_price_per_piece = round($val / $ppb, 2);
            } else {
                // by_kg, std, by_pieces
                $product->purchase_price_per_piece = $val;
                $product->purchase_price_per_box = round($val * $ppb, 2);
            }

            // Recalculate variant prices
            if (!empty($variants)) {
                foreach ($variants as &$v) {
                    if (in_array($product->size_mode, ['by_kg', 'by_meter', 'by_feet'])) {
                        $conv = (float) ($v['conv_factor'] ?? 0);
                        if ($conv <= 0 && isset($v['weight_per_piece'])) {
                            if ($product->size_mode === 'by_feet') {
                                $conv = (float) $v['weight_per_piece'] / 12.0;
                            } elseif ($product->size_mode === 'by_meter') {
                                $conv = (float) $v['weight_per_piece'] / 1000.0;
                            } else {
                                $conv = (float) $v['weight_per_piece'] / 1000.0;
                            }
                        }
                        if ($conv > 0) {
                            $v['purch_price'] = round($val * $conv, 2);
                        } else {
                            $v['purch_price'] = $val;
                        }
                    } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                        $v['purch_price'] = round($val / $ppb, 2);
                    } else {
                        $v['purch_price'] = $val;
                    }
                }
                $product->color = json_encode($variants);
            }
        } elseif ($field === 'sale_price') {
            if ($product->size_mode === 'by_size') {
                $product->price_per_m2 = $val;
                $product->sale_price_per_piece = round($val * $ppm2, 2);
            } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                $product->sale_price_per_box = $val;
                $product->sale_price_per_piece = round($val / $ppb, 2);
            } else {
                // by_kg, std, by_pieces
                $product->sale_price_per_piece = $val;
                $product->sale_price_per_box = round($val * $ppb, 2);
            }

            // Recalculate variant prices
            if (!empty($variants)) {
                foreach ($variants as &$v) {
                    if (in_array($product->size_mode, ['by_kg', 'by_meter', 'by_feet'])) {
                        $conv = (float) ($v['conv_factor'] ?? 0);
                        if ($conv <= 0 && isset($v['weight_per_piece'])) {
                            if ($product->size_mode === 'by_feet') {
                                $conv = (float) $v['weight_per_piece'] / 12.0;
                            } elseif ($product->size_mode === 'by_meter') {
                                $conv = (float) $v['weight_per_piece'] / 1000.0;
                            } else {
                                $conv = (float) $v['weight_per_piece'] / 1000.0;
                            }
                        }
                        if ($conv > 0) {
                            $v['sale_price']   = round($val * $conv, 2);
                            $v['retail_price'] = round($val * $conv, 2);
                        } else {
                            $v['sale_price']   = $val;
                            $v['retail_price'] = $val;
                        }
                    } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                        $v['sale_price']   = round($val / $ppb, 2);
                        $v['retail_price'] = round($val / $ppb, 2);
                    } else {
                        $v['sale_price']   = $val;
                        $v['retail_price'] = $val;
                    }
                }
                $product->color = json_encode($variants);
            }
        } elseif ($field === 'wholesale_price') {
            $product->wholesale_price = $val;

            // Recalculate variant prices
            if (!empty($variants)) {
                foreach ($variants as &$v) {
                    if (in_array($product->size_mode, ['by_kg', 'by_meter', 'by_feet'])) {
                        $conv = (float) ($v['conv_factor'] ?? 0);
                        if ($conv <= 0 && isset($v['weight_per_piece'])) {
                            if ($product->size_mode === 'by_feet') {
                                $conv = (float) $v['weight_per_piece'] / 12.0;
                            } elseif ($product->size_mode === 'by_meter') {
                                $conv = (float) $v['weight_per_piece'] / 1000.0;
                            } else {
                                $conv = (float) $v['weight_per_piece'] / 1000.0;
                            }
                        }
                        if ($conv > 0) {
                            $v['wholesale_price'] = round($val * $conv, 2);
                        } else {
                            $v['wholesale_price'] = $val;
                        }
                    } elseif ($product->size_mode === 'by_cartons' || $product->size_mode === 'by_box') {
                        $v['wholesale_price'] = round($val / $ppb, 2);
                    } else {
                        $v['wholesale_price'] = $val;
                    }
                }
                $product->color = json_encode($variants);
            }
        } elseif ($field === 'alert_quantity') {
            $product->alert_quantity = $val;
            $product->alert_carton_quantity = $val;
        }

        $product->save();

        // Calculate updated stock value and margin matching ReportingController
        $costData = self::calculateProductAverageCost($product);
        $avgPurch = $costData['avg_purchase_price'];
        $newStockValue = $costData['stock_value'];
        $currentStock = $costData['stock_pieces'];

        $basePurch = match ($product->size_mode) {
            'by_size' => (float) $product->purchase_price_per_m2,
            'by_cartons', 'by_box' => (float) $product->purchase_price_per_box,
            default => (float) $product->purchase_price_per_piece,
        };

        $baseSale = match ($product->size_mode) {
            'by_size' => (float) $product->price_per_m2,
            'by_cartons', 'by_box' => (float) $product->sale_price_per_box,
            default => (float) ($product->sale_price_per_piece ?: $product->sale_price_per_box ?: 0),
        };

        $marginPct = $baseSale > 0 ? ((($baseSale - $avgPurch) / $baseSale) * 100) : 0;
        $isLowStock = ($product->alert_quantity > 0 && $currentStock <= $product->alert_quantity);

        return response()->json([
            'success'            => true,
            'message'            => 'Product updated successfully.',
            'product_id'         => $product->id,
            'field'              => $field,
            'base_purchase_price'=> $basePurch,
            'base_sale_price'    => $baseSale,
            'wholesale_price'    => (float) ($product->wholesale_price ?? 0),
            'avg_purchase_price' => $avgPurch,
            'alert_quantity'     => (float) $product->alert_quantity,
            'stock_value'        => $newStockValue,
            'margin_pct'         => round($marginPct, 1),
            'is_low_stock'       => $isLowStock,
            'variants'           => $variants,
        ]);
    }

    /**
     * AJAX endpoint to update product item_code with series & strict duplicate prevention.
     */
    public function updateItemCode(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'item_code'  => 'required|string|max:191',
            'prefix'     => 'nullable|string|max:50',
        ]);

        return $this->processItemCodeUpdate(
            (int) $validated['product_id'],
            trim($validated['item_code']),
            trim($request->input('prefix', ''))
        );
    }

    /**
     * Common logic to update item_code, check duplicates, and sync series counter.
     */
    private function processItemCodeUpdate(int $productId, string $rawCode, string $prefix)
    {
        if (empty($rawCode)) {
            return response()->json([
                'success' => false,
                'message' => 'Item code cannot be empty.',
            ], 422);
        }

        // Compute full code: prepend prefix if rawCode is pure numeric, else keep as-is
        $fullCode = (is_numeric($rawCode) && !empty($prefix)) ? ($prefix . $rawCode) : $rawCode;

        // Strict duplicate check against other products (case-insensitive)
        $duplicate = Product::whereRaw('LOWER(item_code) = ?', [strtolower($fullCode)])
            ->where('id', '!=', $productId)
            ->first();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Item code "' . $fullCode . '" already exists (assigned to "' . $duplicate->item_name . '" [ID: ' . $duplicate->id . ']). Duplicate item codes are not allowed!',
            ], 422);
        }

        $product = Product::findOrFail($productId);
        $oldCode = $product->item_code;
        $product->item_code = $fullCode;
        $product->save();

        // Update Series next_number if applicable
        if (!empty($prefix)) {
            $series = ItemCodeSeries::where('prefix', $prefix)->first();
            if ($series) {
                $numPart = str_replace($prefix, '', $fullCode);
                if (is_numeric($numPart) && (int)$numPart >= $series->next_number) {
                    $series->next_number = (int)$numPart + 1;
                    $series->save();
                }
            }
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Item code updated successfully to "' . $fullCode . '".',
            'product_id'=> $product->id,
            'item_code' => $fullCode,
            'old_code'  => $oldCode,
        ]);
    }
}
