<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
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
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
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
                $query->where('size_mode', 'by_cartons');
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
                'by_size' => 'M²',
                default => 'Pcs',
            };

            // Base Purchase Price & Base Sale Price
            if ($product->size_mode === 'by_size') {
                $basePurchPrice = (float) ($product->purchase_price_per_m2 ?? 0);
                $baseSalePrice  = (float) ($product->price_per_m2 ?? 0);
            } elseif ($product->size_mode === 'by_cartons') {
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

            return (object) [
                'id'                   => $product->id,
                'item_code'            => $product->item_code,
                'item_name'            => $product->item_name,
                'barcode'              => $product->barcode,
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

        return view('admin_panel.product.pricing_stock_value', compact(
            'paginator',
            'items',
            'categories',
            'brands',
            'units',
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
            'by_cartons' => (float) ($product->purchase_price_per_box ?? 0),
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
            'product_id' => 'required|exists:products,id',
            'field'      => 'required|in:purchase_price,sale_price,alert_quantity',
            'value'      => 'required|numeric|min:0',
        ]);

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

        if ($field === 'purchase_price') {
            if ($product->size_mode === 'by_size') {
                $product->purchase_price_per_m2 = $val;
                $product->purchase_price_per_piece = round($val * $ppm2, 2);
            } elseif ($product->size_mode === 'by_cartons') {
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
                    if ($product->size_mode === 'by_kg') {
                        $conv = (float) ($v['conv_factor'] ?? 0);
                        if ($conv <= 0 && isset($v['weight_per_piece'])) {
                            $conv = (float) $v['weight_per_piece'] / 1000.0;
                        }
                        if ($conv > 0) {
                            $v['purch_price'] = round($val * $conv, 2);
                        } else {
                            $v['purch_price'] = $val;
                        }
                    } elseif ($product->size_mode === 'by_cartons') {
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
            } elseif ($product->size_mode === 'by_cartons') {
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
                    if ($product->size_mode === 'by_kg') {
                        $conv = (float) ($v['conv_factor'] ?? 0);
                        if ($conv <= 0 && isset($v['weight_per_piece'])) {
                            $conv = (float) $v['weight_per_piece'] / 1000.0;
                        }
                        if ($conv > 0) {
                            $v['sale_price']   = round($val * $conv, 2);
                            $v['retail_price'] = round($val * $conv, 2);
                        } else {
                            $v['sale_price']   = $val;
                            $v['retail_price'] = $val;
                        }
                    } elseif ($product->size_mode === 'by_cartons') {
                        $v['sale_price']   = round($val / $ppb, 2);
                        $v['retail_price'] = round($val / $ppb, 2);
                    } else {
                        $v['sale_price']   = $val;
                        $v['retail_price'] = $val;
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
            'by_cartons' => (float) $product->purchase_price_per_box,
            default => (float) $product->purchase_price_per_piece,
        };

        $baseSale = match ($product->size_mode) {
            'by_size' => (float) $product->price_per_m2,
            'by_cartons' => (float) $product->sale_price_per_box,
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
            'avg_purchase_price' => $avgPurch,
            'alert_quantity'     => (float) $product->alert_quantity,
            'stock_value'        => $newStockValue,
            'margin_pct'         => round($marginPct, 1),
            'is_low_stock'       => $isLowStock,
            'variants'           => $variants,
        ]);
    }
}
