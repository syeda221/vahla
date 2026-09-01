<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Subcategory;
use App\Models\WarehouseStock;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProductImportExportController extends Controller
{
    // ──────────────────────────────────────────────────────────
    //  TEMPLATE  –  blank CSV with correct headers and example variants
    // ──────────────────────────────────────────────────────────
    public function template()
    {
        $headers = $this->csvHeaders();

        $callback = function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            // New column order: Item Code, Product Name, Category, Sub Category, Brand,
            // Size Mode, Pieces Per Box, Sale Discount%, Purchase Discount%, Is Active,
            // Variant Name, Variant Size, Variant Color, Variant Unit, Variant Conv Factor,
            // Variant Piece Wt (g), Variant Sale Price, Variant Wholesale, Variant Purch Price,
            // Variant Alert, Variant Barcode, Variant Is Base, Variant Stock

            // Row 1: Product A – Variant 1 (by_pieces, base row)
            fputcsv($handle, [
                'ITEM-0001', 'T-Shirt V-Neck', 'Clothing', 'Shirts', 'Nike',
                'by_pieces', '1',
                '0', '0', '1',
                'T-Shirt V-Neck', 'S', 'Red', 'Pcs', '1', '0',
                '1500', '1400', '1000', '0', '876543210001', '1', '50',
            ]);

            // Row 2: Product A – Variant 2 (child variant)
            fputcsv($handle, [
                'ITEM-0001', 'T-Shirt V-Neck', 'Clothing', 'Shirts', 'Nike',
                'by_pieces', '1',
                '0', '0', '1',
                'T-Shirt V-Neck', 'L', 'Blue', 'Pcs', '1', '0',
                '1600', '1500', '1000', '0', '876543210002', '0', '30',
            ]);

            // Row 3: Kg Product – base variant (1 Kg unit)
            fputcsv($handle, [
                'ITEM-0002', 'Steel Wire', 'Hardware', 'Wires', 'General',
                'by_kg', '1',
                '0', '0', '1',
                'Steel Wire', '-', '-', 'Kg', '1', '1000',
                '1000', '950', '700', '0', '876543210003', '1', '10',
            ]);

            // Row 4: Kg Product – child Pcs variant (500g = 0.5 Kg)
            fputcsv($handle, [
                'ITEM-0002', 'Steel Wire', 'Hardware', 'Wires', 'General',
                'by_kg', '1',
                '0', '0', '1',
                'Steel Wire 500g Roll', '-', '-', 'Pcs', '0.5', '500',
                '500', '470', '350', '5', '876543210004', '0', '20',
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  EXPORT  –  all products as CSV (Splitting Variants)
    // ──────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $query = Product::with([
            'category_relation',
            'sub_category_relation',
            'brand'
        ])->orderBy('id');
        
        // Basic filtering
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $products = $query->get();
        $headers = $this->csvHeaders();

        $callback = function () use ($products, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($products as $p) {
                $sizeMode    = $p->size_mode ?? 'by_pieces';
                $pcsPerBox   = $p->pieces_per_box > 0 ? $p->pieces_per_box : 1;
                
                $variants = [];
                if (!empty($p->color)) {
                    $parsed = is_string($p->color) ? json_decode($p->color, true) : $p->color;
                    if (is_array($parsed) && count($parsed) > 0 && isset($parsed[0]['name'])) {
                        $variants = $parsed;
                    }
                }
                
                // Fallback to single variant row if no variants defined
                if (empty($variants)) {
                    $ws = WarehouseStock::where('product_id', $p->id)->first();
                    $stockPieces = $ws ? $ws->total_pieces : 0;
                    
                    $vUnit = $sizeMode === 'by_kg' ? 'Kg' : ($sizeMode === 'by_gm' ? 'Gm' : 'Pcs');
                    fputcsv($handle, [
                        $p->item_code,
                        $p->item_name,
                        $p->category_relation->name ?? '',
                        $p->sub_category_relation->name ?? '',
                        $p->brand->name ?? '',
                        $sizeMode,
                        $pcsPerBox,
                        $p->sale_discount_percent ?? 0,
                        $p->purchase_discount_percent ?? 0,
                        $p->is_active ? 1 : 0,
                        // Variant columns
                        $p->item_name,  // Variant Name
                        '-',            // Variant Size
                        '-',            // Variant Color
                        $vUnit,         // Variant Unit
                        '1',            // Variant Conv Factor
                        $vUnit === 'Kg' ? '1000' : '0', // Variant Piece Wt (g)
                        round($p->sale_price_per_piece ?? 0, 2),
                        round($p->wholesale_price ?? 0, 2),
                        round($p->purchase_price_per_piece ?? 0, 2),
                        '0',            // Variant Alert
                        $p->barcode_path, // Variant Barcode
                        '1',            // Variant Is Base
                        $stockPieces,   // Variant Stock
                    ]);
                } else {
                    foreach ($variants as $v) {
                        $vUnit = $v['unit'] ?? ($sizeMode === 'by_kg' ? 'Kg' : ($sizeMode === 'by_gm' ? 'Gm' : 'Pcs'));
                        $vFactor = isset($v['conv_factor']) ? $v['conv_factor'] : 1;
                        $vWeight = isset($v['weight_per_piece']) ? $v['weight_per_piece'] : (($vFactor > 0 && $vFactor < 10) ? $vFactor * 1000 : 0);
                        $vIsBase = isset($v['is_base_variant']) ? (int)$v['is_base_variant'] : 0;
                        $vAlert  = $v['alert'] ?? 0;

                        fputcsv($handle, [
                            $p->item_code,
                            $p->item_name,
                            $p->category_relation->name ?? '',
                            $p->sub_category_relation->name ?? '',
                            $p->brand->name ?? '',
                            $sizeMode,
                            $pcsPerBox,
                            $p->sale_discount_percent ?? 0,
                            $p->purchase_discount_percent ?? 0,
                            $p->is_active ? 1 : 0,
                            // Variant columns
                            $v['name'] ?? $p->item_name,
                            $v['size'] ?? '-',
                            $v['color'] ?? '-',
                            $vUnit,
                            $vFactor,
                            $vWeight,
                            round($v['sale_price'] ?? $p->sale_price_per_piece ?? 0, 2),
                            round($v['wholesale_price'] ?? $p->wholesale_price ?? 0, 2),
                            round($v['purch_price'] ?? $p->purchase_price_per_piece ?? 0, 2),
                            $vAlert,
                            $v['barcode'] ?? $p->barcode_path,
                            $vIsBase,
                            $v['stock'] ?? 0,
                        ]);
                    }
                }
            }
            fclose($handle);
        };

        $filename = 'products_export_' . now()->format('Y-m-d_H-i') . '.csv';

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    // ──────────────────────────────────────────────────────────
    //  IMPORT STEP 1: VALIDATE & PREVIEW
    // ──────────────────────────────────────────────────────────
    public function importValidate(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'import_mode' => 'required|in:create,update_only',
        ]);

        $mode = $request->input('import_mode');
        $autoCreate = $request->has('auto_create');
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $headerRow = fgetcsv($handle);
        if (!$headerRow) {
            return redirect()->back()->with('error', 'CSV file is empty or invalid.');
        }

        $headerMap = [];
        foreach ($headerRow as $i => $col) {
            $headerMap[strtolower(trim($col))] = $i;
        }
        
        // Mapping accepted header names (Product Reference is optional now)
        $requiredCols = [
            'product_name'    => ['product name', 'product_name', 'item_name', 'item_name (*)'],
            'variant_name'    => ['variant name', 'variant_name', 'item_name (*)'],
            'sale_price'      => ['variant sale price', 'sale_price', 'variant_sale_price_per_piece', 'sale_price_per_piece'],
            'wholesale_price' => ['variant wholesale', 'variant wholesale price', 'wholesale_price'],
            'purch_price'     => ['variant purch price', 'variant purchase price', 'purchase_price', 'variant_purchase_price_per_piece', 'purchase_price_per_piece'],
        ];

        $matchedHeaders = [];
        foreach ($requiredCols as $key => $possibleNames) {
            $found = false;
            foreach ($possibleNames as $pName) {
                if (isset($headerMap[$pName])) {
                    $matchedHeaders[$key] = $headerMap[$pName];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return redirect()->back()->with('error', "Required column matching '{$possibleNames[0]}' not found. Please use the downloaded template.");
            }
        }

        $get = function (array $row, $possibleNames, $default = '') use ($headerMap) {
            if (!is_array($possibleNames)) $possibleNames = [$possibleNames];
            foreach ($possibleNames as $pName) {
                $key = strtolower(trim($pName));
                if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                    return trim($row[$headerMap[$key]]);
                }
            }
            return $default;
        };

        $categories = Category::pluck('name', 'id')->map(function($name) { return strtolower($name); })->toArray();
        $subCategories = Subcategory::pluck('name', 'id')->map(function($name) { return strtolower($name); })->toArray();
        $brands = Brand::pluck('name', 'id')->map(function($name) { return strtolower($name); })->toArray();
        
        $productsByRef = [];
        $errors = [];
        $rowNum = 1;
        $barcodesSeen = [];
        
        // For preview tracking
        $masterDataToCreate = [
            'categories' => [],
            'subcategories' => [],
            'brands' => []
        ];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if (empty(array_filter($row))) continue;

            $prodRef = $get($row, ['item code', 'item_code', 'product reference', 'product_reference', 'id']);
            $prodName = $get($row, $requiredCols['product_name']);
            
            $isAutoGen = false;
            if (empty($prodRef)) {
                $prodRef = 'AUTO_GEN_' . $rowNum;
                $isAutoGen = true;
            }
            
            if (empty($prodName)) {
                $errors[] = ["row" => $rowNum, "msg" => "Product Name is required."];
                continue;
            }

            $catName = $get($row, ['category']);
            $subCatName = $get($row, ['sub_category', 'sub category']);
            $brandName = $get($row, ['brand']);
            $vBarcode = $get($row, ['variant barcode', 'barcode']);
            
            if (!empty($vBarcode)) {
                if (isset($barcodesSeen[$vBarcode])) {
                    $errors[] = ["row" => $rowNum, "msg" => "Duplicate barcode '$vBarcode' found in CSV (Row ".$barcodesSeen[$vBarcode].")."];
                }
                $barcodesSeen[$vBarcode] = $rowNum;
            }
            
            // Check master data
            if (!empty($catName) && !in_array(strtolower($catName), $categories)) {
                if ($autoCreate) {
                    if (!in_array($catName, $masterDataToCreate['categories'])) {
                        $masterDataToCreate['categories'][] = $catName;
                    }
                } else {
                    $errors[] = ["row" => $rowNum, "msg" => "Category '$catName' does not exist."];
                }
            }
            
            if (!empty($brandName) && !in_array(strtolower($brandName), $brands)) {
                if ($autoCreate) {
                    if (!in_array($brandName, $masterDataToCreate['brands'])) {
                        $masterDataToCreate['brands'][] = $brandName;
                    }
                } else {
                    $errors[] = ["row" => $rowNum, "msg" => "Brand '$brandName' does not exist."];
                }
            }

            if (!isset($productsByRef[$prodRef])) {
                $rawSizeMode = strtolower(trim($get($row, ['size mode', 'size_mode', 'mode'], 'by_pieces')));
                $sizeMode = 'by_pieces';
                if (in_array($rawSizeMode, ['by_kg', 'kg', 'kgs', 'kilogram', 'kilograms'])) {
                    $sizeMode = 'by_kg';
                } elseif (in_array($rawSizeMode, ['by_gm', 'gm', 'g', 'gram', 'grams'])) {
                    $sizeMode = 'by_gm';
                } elseif (in_array($rawSizeMode, ['by_cartons', 'carton', 'cartons'])) {
                    $sizeMode = 'by_cartons';
                } elseif (in_array($rawSizeMode, ['by_feet', 'ft', 'feet'])) {
                    $sizeMode = 'by_feet';
                } elseif (in_array($rawSizeMode, ['by_meter', 'meter', 'mtr'])) {
                    $sizeMode = 'by_meter';
                }

                $productsByRef[$prodRef] = [
                    'ref' => $prodRef,
                    'name' => $prodName,
                    'category' => $catName,
                    'sub_category' => $subCatName,
                    'brand' => $brandName,
                    'size_mode' => $sizeMode,
                    'pcs_per_carton' => max(1, (int)$get($row, ['pcs per box', 'pieces per box', 'pcs_per_carton'], 1)),
                    'sale_discount' => max(0, (float)$get($row, ['sale discount %', 'sale_discount_%'], 0)),
                    'purch_discount' => max(0, (float)$get($row, ['purchase discount %', 'purchase_discount_%'], 0)),
                    'is_active' => (int)$get($row, ['is_active', 'is active'], 1),
                    'variants' => []
                ];
            } else {
                // Check conflicting product info
                if (strtolower($productsByRef[$prodRef]['name']) !== strtolower($prodName)) {
                    $errors[] = ["row" => $rowNum, "msg" => "Conflicting product name for reference '$prodRef'."];
                }
            }

            $vUnit = $get($row, ['variant unit', 'variant_unit', 'unit'], ($productsByRef[$prodRef]['size_mode'] === 'by_kg' ? 'Kg' : 'Pcs'));
            $vPieceWt = (float) $get($row, ['variant piece wt (g)', 'variant piece weight (g)', 'variant_weight_per_piece', 'weight_per_piece', 'piece_weight'], 0);
            $rawConvFactor = $get($row, ['variant conv factor', 'variant_conv_factor', 'conv_factor', 'factor'], '');
            $vConvFactor = $rawConvFactor !== '' ? (float)$rawConvFactor : 0;
            $vIsBase = (int) $get($row, ['variant is base', 'variant_is_base', 'is_base'], 0);
            $vAlert  = (float) $get($row, ['variant alert', 'variant_alert_qty', 'alert'], 0);

            // Auto-calculate conversion factor from piece weight (g) if empty or not provided
            if ($vPieceWt > 0 && ($rawConvFactor === '' || $vConvFactor <= 0 || $vConvFactor == 1)) {
                $vConvFactor = $vPieceWt / 1000.0;
            } elseif ($vPieceWt <= 0 && $vConvFactor > 0 && $vConvFactor < 1) {
                $vPieceWt = $vConvFactor * 1000.0;
            } elseif ($vConvFactor <= 0) {
                $vConvFactor = 1;
            }

            // Variant Data
            $productsByRef[$prodRef]['variants'][] = [
                'row'             => $rowNum,
                'name'            => $get($row, $requiredCols['variant_name'], $prodName),
                'size'            => $get($row, ['variant size', 'variant_size'], '-'),
                'color'           => $get($row, ['variant color', 'variant_color'], '-'),
                'unit'            => $vUnit,
                'conv_factor'     => $vConvFactor,
                'weight_per_piece'=> $vPieceWt,
                'barcode'         => $vBarcode,
                'stock'           => max(0, (float)$get($row, ['variant stock', 'variant stock pieces', 'variant_stock', 'stock_total_pieces'], 0)),
                'sale_price'      => max(0, (float)$get($row, $requiredCols['sale_price'], 0)),
                'wholesale_price' => max(0, (float)$get($row, $requiredCols['wholesale_price'], 0)),
                'purch_price'     => max(0, (float)$get($row, $requiredCols['purch_price'], 0)),
                'is_base_variant' => $vIsBase,
                'alert'           => $vAlert,
            ];
        }

        fclose($handle);

        // Prepare Preview Summary
        $existingCodes = Product::pluck('id', 'item_code')->toArray();
        $productsToCreate = 0;
        $productsToUpdate = 0;
        $variantsToCreate = 0;
        $variantsToUpdate = 0;
        $ignored = 0;
        
        $validPayload = [];

        foreach ($productsByRef as $ref => $pData) {
            $isUpdate = isset($existingCodes[$ref]);
            
            if ($isUpdate) {
                $existingProduct = Product::find($existingCodes[$ref]);
                $existingVariants = [];
                if (!empty($existingProduct->color)) {
                    $parsed = is_string($existingProduct->color) ? json_decode($existingProduct->color, true) : $existingProduct->color;
                    if (is_array($parsed)) $existingVariants = $parsed;
                }
                
                $newVCount = 0;
                $updVCount = 0;
                $filteredVariants = [];
                
                foreach ($pData['variants'] as $newV) {
                    $found = false;
                    foreach ($existingVariants as $eV) {
                        if (isset($eV['name']) && strcasecmp($eV['name'], $newV['name']) === 0) {
                            if (isset($eV['size']) && strcasecmp($eV['size'], $newV['size']) === 0) {
                                if (isset($eV['color']) && strcasecmp($eV['color'], $newV['color']) === 0) {
                                    $found = true;
                                    break;
                                }
                            }
                        }
                    }
                    if ($found) {
                        $updVCount++;
                        $filteredVariants[] = $newV;
                    } else {
                        $newVCount++;
                        $filteredVariants[] = $newV;
                    }
                }
                
                $pData['variants'] = $filteredVariants;
                if (empty($filteredVariants)) {
                    continue; // Skip product completely if all variants ignored
                }
                
                $productsToUpdate++;
                $variantsToUpdate += $updVCount;
                $variantsToCreate += $newVCount;
                
            } else {
                if ($mode === 'update_only') {
                    $ignored += count($pData['variants']);
                    continue; // Skip new products entirely
                }
                $productsToCreate++;
                $variantsToCreate += count($pData['variants']);
            }
            
            $validPayload[$ref] = $pData;
        }

        // Store payload in session for confirmation
        Session::put('import_payload', [
            'mode' => $mode,
            'auto_create' => $autoCreate,
            'products' => $validPayload,
            'master_data' => $masterDataToCreate,
            'errors' => $errors,
            'preview_stats' => [
                'products_create' => $productsToCreate,
                'products_update' => $productsToUpdate,
                'variants_create' => $variantsToCreate,
                'variants_update' => $variantsToUpdate,
                'ignored' => $ignored,
                'master_create' => count($masterDataToCreate['categories']) + count($masterDataToCreate['brands'])
            ]
        ]);

        return redirect()->route('products.import.preview');
    }
    
    // ──────────────────────────────────────────────────────────
    //  IMPORT STEP 2: SHOW PREVIEW
    // ──────────────────────────────────────────────────────────
    public function importPreview()
    {
        if (!Session::has('import_payload')) {
            return redirect()->route('product')->with('error', 'Import session expired or invalid.');
        }
        
        $payload = Session::get('import_payload');
        return view('admin_panel.product.import_preview', compact('payload'));
    }
    
    // ──────────────────────────────────────────────────────────
    //  IMPORT STEP 3: CONFIRM & IMPORT
    // ──────────────────────────────────────────────────────────
    public function importConfirm()
    {
        if (!Session::has('import_payload')) {
            return redirect()->route('product')->with('error', 'Import session expired.');
        }

        $payload = Session::get('import_payload');
        $productsToProcess = $payload['products'];
        $autoCreate = $payload['auto_create'];
        
        $createdProducts = 0;
        $updatedProducts = 0;
        $createdVariants = 0;
        $updatedVariants = 0;

        DB::beginTransaction();
        try {
            // 1. Auto Create Master Data
            $catMap = Category::pluck('id', 'name')->mapWithKeys(function ($item, $key) { return [strtolower($key) => $item]; })->toArray();
            $brandMap = Brand::pluck('id', 'name')->mapWithKeys(function ($item, $key) { return [strtolower($key) => $item]; })->toArray();
            $subCatMap = Subcategory::pluck('id', 'name')->mapWithKeys(function ($item, $key) { return [strtolower($key) => $item]; })->toArray();
            
            if ($autoCreate) {
                foreach ($payload['master_data']['categories'] as $catName) {
                    $key = strtolower($catName);
                    if (!isset($catMap[$key])) {
                        $c = Category::create(['name' => $catName]);
                        $catMap[$key] = $c->id;
                    }
                }
                foreach ($payload['master_data']['brands'] as $brandName) {
                    $key = strtolower($brandName);
                    if (!isset($brandMap[$key])) {
                        $b = Brand::create(['name' => $brandName]);
                        $brandMap[$key] = $b->id;
                    }
                }
            }

            // 2. Process Products
            foreach ($productsToProcess as $ref => $pData) {
                $product = Product::where('item_code', $ref)->first();
                
                $cId = isset($catMap[strtolower($pData['category'])]) ? $catMap[strtolower($pData['category'])] : null;
                $bId = isset($brandMap[strtolower($pData['brand'])]) ? $brandMap[strtolower($pData['brand'])] : null;
                $sId = isset($subCatMap[strtolower($pData['sub_category'])]) ? $subCatMap[strtolower($pData['sub_category'])] : null;
                
                // Compile final variant array
                $finalVariants = [];
                $existingTotalStock = 0;
                
                if ($product) {
                    // Update
                    $existingVariants = [];
                    if (!empty($product->color)) {
                        $parsed = is_string($product->color) ? json_decode($product->color, true) : $product->color;
                        if (is_array($parsed)) $existingVariants = $parsed;
                    }
                    
                    // If mode is 'update_only', we strictly sync (delete omitted).
                    // If mode is 'create', we preserve all existing variants and merge/append the CSV ones.
                    if ($payload['mode'] === 'update_only') {
                        $finalVariants = []; // Wipe and replace
                    } else {
                        $finalVariants = $existingVariants; // Preserve and merge
                    }
                    
                    foreach ($pData['variants'] as $newV) {
                        $foundIdx = -1;
                        $oldBarcode = '';
                        
                        foreach ($finalVariants as $idx => $eV) {
                            if (strcasecmp($eV['name'] ?? '', $newV['name']) === 0 && 
                                strcasecmp($eV['size'] ?? '', $newV['size']) === 0 && 
                                strcasecmp($eV['color'] ?? '', $newV['color']) === 0) {
                                $foundIdx = $idx;
                                $oldBarcode = $eV['barcode'] ?? '';
                                break;
                            }
                        }
                        
                        $vArr = [
                            'name'             => $newV['name'],
                            'size'             => $newV['size'],
                            'color'            => $newV['color'],
                            'unit'             => $newV['unit'] ?? ($pData['size_mode'] === 'by_kg' ? 'Kg' : 'Pcs'),
                            'conv_factor'      => (float)($newV['conv_factor'] ?? 1),
                            'weight_per_piece' => (float)($newV['weight_per_piece'] ?? 0),
                            'stock'            => $newV['stock'],
                            'sale_price'       => $newV['sale_price'],
                            'wholesale_price'  => $newV['wholesale_price'] ?? 0,
                            'purch_price'      => $newV['purch_price'],
                            'barcode'          => $newV['barcode'] ?: $oldBarcode,
                            'is_base_variant'  => (int)($newV['is_base_variant'] ?? 0),
                            'alert'            => (float)($newV['alert'] ?? 0),
                        ];
                        
                        if ($foundIdx >= 0) {
                            $finalVariants[$foundIdx] = $vArr;
                            $updatedVariants++;
                        } else {
                            $finalVariants[] = $vArr;
                            $createdVariants++;
                        }
                    }
                    
                    $stockTotal = 0;
                    foreach ($finalVariants as $v) {
                        $stk = (float) ($v['stock'] ?? 0);
                        $f = (float) ($v['conv_factor'] ?? 1);
                        $u = strtolower($v['unit'] ?? '');
                        if (($pData['size_mode'] === 'by_kg' || $pData['size_mode'] === 'by_gm') && ($u === 'pcs' || $u === 'pc') && $f > 0) {
                            $stockTotal += ($stk * $f);
                        } elseif ($u === 'gm' || $u === 'g') {
                            $stockTotal += ($stk / 1000.0);
                        } else {
                            $stockTotal += $stk;
                        }
                    }
                    
                    $product->update([
                        'item_name' => $pData['name'],
                        'category_id' => $cId,
                        'sub_category_id' => $sId,
                        'brand_id' => $bId,
                        'size_mode' => $pData['size_mode'],
                        'pieces_per_box' => $pData['pcs_per_carton'],
                        'sale_discount_percent' => $pData['sale_discount'],
                        'purchase_discount_percent' => $pData['purch_discount'],
                        'sale_price_per_piece' => $finalVariants[0]['sale_price'] ?? 0,
                        'wholesale_price' => $finalVariants[0]['wholesale_price'] ?? 0,
                        'purchase_price_per_piece' => $finalVariants[0]['purch_price'] ?? 0,
                        'color' => json_encode($finalVariants),
                        'is_active' => $pData['is_active'],
                    ]);
                    
                    $ws = WarehouseStock::firstOrNew([
                        'warehouse_id' => 1,
                        'product_id' => $product->id
                    ]);
                    $diff = $stockTotal - ($ws->total_pieces ?? 0);
                    $ws->total_pieces = $stockTotal;
                    $ws->quantity = floor($stockTotal / max(1, $pData['pcs_per_carton']));
                    $ws->remarks = 'Updated via Bulk Import';
                    $ws->save();
                    
                    if ($diff != 0) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type'       => 'adjustment',
                            'qty'        => $diff,
                            'ref_type'   => 'IMPORT',
                            'note'       => 'Stock adjusted via CSV import',
                        ]);
                    }
                    
                    $updatedProducts++;
                    
                } else {
                    foreach ($pData['variants'] as $newV) {
                        $finalVariants[] = [
                            'name'             => $newV['name'],
                            'size'             => $newV['size'],
                            'color'            => $newV['color'],
                            'unit'             => $newV['unit'] ?? ($pData['size_mode'] === 'by_kg' ? 'Kg' : 'Pcs'),
                            'conv_factor'      => (float)($newV['conv_factor'] ?? 1),
                            'weight_per_piece' => (float)($newV['weight_per_piece'] ?? 0),
                            'stock'            => $newV['stock'],
                            'sale_price'       => $newV['sale_price'],
                            'wholesale_price'  => $newV['wholesale_price'] ?? 0,
                            'purch_price'      => $newV['purch_price'],
                            'barcode'          => $newV['barcode'],
                            'is_base_variant'  => (int)($newV['is_base_variant'] ?? 0),
                            'alert'            => (float)($newV['alert'] ?? 0),
                        ];
                        $createdVariants++;
                    }
                    
                    $stockTotal = 0;
                    foreach ($finalVariants as $v) {
                        $stk = (float) ($v['stock'] ?? 0);
                        $f = (float) ($v['conv_factor'] ?? 1);
                        $u = strtolower($v['unit'] ?? '');
                        if (($pData['size_mode'] === 'by_kg' || $pData['size_mode'] === 'by_gm') && ($u === 'pcs' || $u === 'pc') && $f > 0) {
                            $stockTotal += ($stk * $f);
                        } elseif ($u === 'gm' || $u === 'g') {
                            $stockTotal += ($stk / 1000.0);
                        } else {
                            $stockTotal += $stk;
                        }
                    }
                    
                    // Always generate a fresh ITEM-XXXX code for new products
                    $lastProduct = Product::orderBy('id', 'desc')->first();
                    $actualRef = $lastProduct ? ('ITEM-'.str_pad($lastProduct->id + 1, 4, '0', STR_PAD_LEFT)) : 'ITEM-0001';
                    
                    $product = Product::create([
                        'creater_id' => Auth::id(),
                        'item_code' => $actualRef,
                        'item_name' => $pData['name'],
                        'category_id' => $cId,
                        'sub_category_id' => $sId,
                        'brand_id' => $bId,
                        'size_mode' => $pData['size_mode'],
                        'pieces_per_box' => $pData['pcs_per_carton'],
                        'sale_discount_percent' => $pData['sale_discount'],
                        'purchase_discount_percent' => $pData['purch_discount'],
                        'sale_price_per_piece' => $finalVariants[0]['sale_price'] ?? 0,
                        'wholesale_price' => $finalVariants[0]['wholesale_price'] ?? 0,
                        'purchase_price_per_piece' => $finalVariants[0]['purch_price'] ?? 0,
                        'color' => json_encode($finalVariants),
                        'is_active' => $pData['is_active'],
                        'is_part' => 0,
                        'is_assembled' => 0,
                        'barcode_path' => rand(100000000000, 999999999999), // Master barcode
                        'total_m2' => 0,
                        'price_per_m2' => 0,
                        'purchase_price_per_m2' => 0,
                        'pieces_per_m2' => 0,
                        'height' => 0,
                        'width' => 0,
                    ]);
                    
                    WarehouseStock::create([
                        'warehouse_id' => 1,
                        'product_id'   => $product->id,
                        'quantity'     => floor($stockTotal / max(1, $pData['pcs_per_carton'])),
                        'total_pieces' => $stockTotal,
                        'remarks'      => 'Initial Stock via Import',
                    ]);
                    
                    if ($stockTotal > 0) {
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type'       => 'adjustment',
                            'qty'        => $stockTotal,
                            'ref_type'   => 'IMPORT',
                            'note'       => 'Initial Stock via CSV import',
                        ]);
                    }
                    
                    $createdProducts++;
                }
            }

            DB::commit();
            Session::forget('import_payload');
            
            return redirect()->route('product')->with('success', "Import completed successfully. {$createdProducts} products created, {$updatedProducts} products updated. {$createdVariants} variants created, {$updatedVariants} variants updated.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('product')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    //  Helper – canonical CSV column headers
    //  ORDER matches create_product form variant columns exactly
    // ──────────────────────────────────────────────────────────
    private function csvHeaders(): array
    {
        return [
            'Item Code',            // Product reference key
            'Product Name',         // item_name
            'Category',
            'Sub Category',
            'Brand',
            'Size Mode',            // by_pieces / by_kg / by_gm / by_cartons / by_size
            'Pieces Per Box',
            'Sale Discount %',
            'Purchase Discount %',
            'Is Active',
            // Variant columns — same order as create_product table headers
            'Variant Name',          // variant_name[]
            'Variant Size',          // variant_size[]
            'Variant Color',         // variant_color[]
            'Variant Unit',          // variant_unit[]: Kg/Pcs/Gm/Ft/Meter/Box/Dozen
            'Variant Conv Factor',   // variant_conv_factor[]
            'Variant Piece Wt (g)',  // variant_weight_per_piece[]
            'Variant Sale Price',    // variant_sale_price[]
            'Variant Wholesale',     // variant_wholesale_price[]
            'Variant Purch Price',   // variant_purchase_price[]
            'Variant Alert',         // variant_alert_qty[]
            'Variant Barcode',       // variant_barcode[]
            'Variant Is Base',       // variant_is_base[]: 1=base row 0=child
            'Variant Stock',         // variant_stock[] (initial stock in base unit)
        ];
    }
}

