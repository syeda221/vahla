<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLink;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class ProductLinkController extends Controller
{
    /**
     * Get all linked products for a specific product.
     */
    public function getLinks($productId)
    {
        $product = Product::select('id', 'item_name', 'item_code')->findOrFail($productId);
        $links = ProductLink::where('product_id', $productId)
            ->with(['linkedProduct.unit'])
            ->get();

        $formatted = $links->map(function ($link) {
            $lp = $link->linkedProduct;
            return [
                'id'                => $link->id,
                'product_id'        => $link->product_id,
                'linked_product_id' => $link->linked_product_id,
                'name'              => $link->variant_name ?: ($lp ? $lp->item_name : 'Unknown Product'),
                'code'              => $lp ? $lp->item_code : '',
                'unit'              => $link->variant_unit ?: ($lp && $lp->unit ? $lp->unit->name : 'Pcs'),
                'variant_key'       => $link->variant_key,
                'default_qty'       => (float)$link->default_qty,
                'variant_data'      => $link->variant_data,
                'created_at'        => $link->created_at ? $link->created_at->format('d M Y') : '',
            ];
        });

        return response()->json([
            'success'  => true,
            'product'  => $product,
            'links'    => $formatted,
        ]);
    }

    /**
     * Store or update a product link.
     */
    public function storeLink(Request $request)
    {
        $request->validate([
            'product_id'        => 'required|exists:products,id',
            'linked_product_id' => 'required|exists:products,id',
            'default_qty'       => 'nullable|numeric|min:0.01',
        ]);

        if ((int)$request->product_id === (int)$request->linked_product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot link a product to itself.',
            ], 422);
        }

        $rawVData = $request->variant_data ?? null;
        $vName = $request->variant_name ?? null;
        $vUnit = $request->variant_unit ?? null;

        if (!empty($rawVData)) {
            $b64 = base64_decode($rawVData, true);
            $parsed = ($b64 !== false) ? json_decode($b64, true) : json_decode($rawVData, true);
            if (is_array($parsed)) {
                if (!empty($parsed['name']) && empty($vName)) $vName = $parsed['name'];
                if (!empty($parsed['unit']) && empty($vUnit)) $vUnit = $parsed['unit'];
            }
        }

        $link = ProductLink::updateOrCreate(
            [
                'product_id'        => $request->product_id,
                'linked_product_id' => $request->linked_product_id,
                'variant_key'       => $request->variant_key ?: null,
            ],
            [
                'variant_unit'      => $vUnit,
                'variant_name'      => $vName,
                'variant_data'      => $rawVData,
                'default_qty'       => max(0.01, (float)($request->default_qty ?? 1)),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Product linked successfully!',
            'link'    => $link->load('linkedProduct.unit'),
        ]);
    }

    /**
     * Delete a product link.
     */
    public function deleteLink($id)
    {
        $link = ProductLink::findOrFail($id);
        $link->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product link removed successfully!',
        ]);
    }

    /**
     * Check if a product has linked products for the Sales Screen.
     */
    public function checkLinkedProducts(Request $request, $productId)
    {
        $warehouseId = (int)$request->input('warehouse_id', auth()->user()->warehouse_id ?? 1);

        $mainProduct = Product::select('id', 'item_name', 'item_code')->find($productId);
        if (!$mainProduct) {
            return response()->json(['has_links' => false]);
        }

        $links = ProductLink::where('product_id', $productId)
            ->with(['linkedProduct.unit', 'linkedProduct.brand'])
            ->get();

        if ($links->isEmpty()) {
            return response()->json(['has_links' => false]);
        }

        $formattedList = [];

        foreach ($links as $link) {
            $p = $link->linkedProduct;
            if (!$p || !$p->is_active) {
                continue;
            }

            // Fetch warehouse stock
            $stockRecord = WarehouseStock::where('product_id', $p->id)
                ->where('warehouse_id', $warehouseId)
                ->first();
            $stockPieces = (float)($stockRecord->total_pieces ?? 0);

            $vData = null;
            if (!empty($link->variant_data)) {
                $b64 = base64_decode($link->variant_data, true);
                $vData = ($b64 !== false) ? json_decode($b64, true) : json_decode($link->variant_data, true);
                if (!is_array($vData)) $vData = null;
            }

            $variantName = !empty($vData['name']) ? $vData['name'] : ($link->variant_name ?: $p->item_name);
            $vUnit = !empty($vData['unit']) ? $vData['unit'] : ($link->variant_unit ?: ($p->unit->name ?? 'Pcs'));
            $convFactor = (float)(isset($vData['conv_factor']) && (float)$vData['conv_factor'] > 0 ? $vData['conv_factor'] : 1);
            $weightPerPiece = (float)(isset($vData['weight_per_piece']) && $vData['weight_per_piece'] !== '' ? $vData['weight_per_piece'] : ($p->weight_per_piece ?? 0));
            $ppb = (float)($p->pieces_per_box > 0 ? $p->pieces_per_box : 1);

            $rate = (float)(!empty($vData['sale_price']) ? $vData['sale_price'] : ($p->sale_price_per_piece > 0 ? $p->sale_price_per_piece : ($p->sale_price_per_box ?? 0)));

            // Compute display stock
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
                        $vStockDisplay = "{$pcsCount} {$vUnit}";
                        $variantStockPieces = $pcsCount;
                    }
                }
            } elseif (($p->size_mode === 'by_cartons' || $p->size_mode === 'by_size') && $ppb > 1) {
                $vBoxes = (int) floor($stockPieces / $ppb);
                $vLoose = (int) round($stockPieces - ($vBoxes * $ppb));
                $vStockDisplay = $vLoose > 0 ? "{$vBoxes}.{$vLoose} Ctn" : "{$vBoxes} Ctn";
                $variantStockPieces = $stockPieces;
            } elseif ($vData && isset($vData['stock'])) {
                if (!$stockRecord && (float)$vData['stock'] > 0) {
                    $variantStockPieces = (float)$vData['stock'];
                    $vStockDisplay = "{$variantStockPieces} {$vUnit}";
                }
            }

            if ($vData) {
                $vData['current_stock'] = $vStockDisplay;
                $variantDataEncoded = base64_encode(json_encode($vData));
            } else {
                $variantDataEncoded = $link->variant_data;
            }

            $formattedList[] = [
                'link_id'              => $link->id,
                'product_id'           => $p->id,
                'item_name'            => $variantName,
                'item_code'            => $p->item_code,
                'barcode'              => $link->variant_key ?: $p->barcode_path,
                'default_qty'          => (float)$link->default_qty,
                'unit_name'            => $vUnit,
                'retail_price'         => $rate,
                'wholesale_price'      => (float)($p->wholesale_price ?? 0),
                'stock'                => $vStockDisplay,
                'stock_pieces'         => $variantStockPieces,
                'size_mode'            => $p->size_mode ?: 'by_pieces',
                'pieces_per_box'       => $ppb,
                'weight_per_piece'     => $weightPerPiece,
                'conv_factor'          => $convFactor,
                'variant_key'          => $link->variant_key,
                'variant_unit'         => $vUnit,
                'variant_name'         => $variantName,
                'variant_data'         => $variantDataEncoded,
            ];
        }

        return response()->json([
            'has_links'       => !empty($formattedList),
            'main_product'    => [
                'id'        => $mainProduct->id,
                'item_name' => $mainProduct->item_name,
                'item_code' => $mainProduct->item_code,
            ],
            'linked_products' => $formattedList,
        ]);
    }
}
