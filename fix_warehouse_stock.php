<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

echo "Calculating stock based on Sales/Purchases (Item Stock Report logic)...\n";

$products = Product::all();
$count = 0;

foreach ($products as $product) {
    // Check if product has variants
    $parsedVariants = [];
    if ($product->color) {
        try {
            $decoded = json_decode($product->color, true);
            if (is_array($decoded) && count($decoded) > 0 && isset($decoded[0]['name'])) {
                $parsedVariants = $decoded;
            }
        } catch (\Exception $e) {}
    }

    $totalCalculatedPieces = 0;

    if (count($parsedVariants) > 0) {
        // Fetch all sales, returns, purchases, and purchase returns for this product
        $salesList = DB::table('sale_items')
            ->where('product_id', $product->id)
            ->select('total_pieces', 'color')
            ->get();

        $returnsList = DB::table('sale_return_items as sri')
            ->join('sale_returns as sr', 'sr.id', '=', 'sri.sale_return_id')
            ->where('sri.product_id', $product->id)
            ->select('sri.qty', 'sri.color', 'sr.sale_id')
            ->get();

        $purchasesList = DB::table('purchase_items as pi')
            ->join('purchases as pur', 'pur.id', '=', 'pi.purchase_id')
            ->where('pi.product_id', $product->id)
            ->whereIn('pur.status_purchase', ['approved', 'Returned', 'Partial'])
            ->select('pi.qty', 'pi.unit', 'pi.pieces_per_box', 'pi.boxes_qty', 'pi.loose_qty', 'pi.color')
            ->get();

        $purchaseReturnsList = DB::table('purchase_return_items as pri')
            ->where('pri.product_id', $product->id)
            ->select('pri.qty', 'pri.color')
            ->get();

        $saleIds = $returnsList->pluck('sale_id')->unique()->toArray();
        $saleItemsMap = [];
        if (!empty($saleIds)) {
            $siList = DB::table('sale_items')
                ->whereIn('sale_id', $saleIds)
                ->where('product_id', $product->id)
                ->select('sale_id', 'color')
                ->get();
            foreach ($siList as $si) {
                $saleItemsMap[$si->sale_id][] = $si->color;
            }
        }

        foreach ($parsedVariants as $v) {
            $initial = (float) ($v['stock'] ?? 0);
            $vPpb = (float) ($v['conv_factor'] ?? ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1));
            if ($vPpb <= 0) $vPpb = 1;

            // Match function locally
            $matchSaleItemToVariant = function($itemColor, $variant) {
                if (empty($itemColor)) return false;
                $itemVariant = [];
                $b64Decoded = base64_decode($itemColor, true);
                if ($b64Decoded !== false) {
                    $json = json_decode($b64Decoded, true);
                    if (is_array($json)) {
                        $itemVariant = $json;
                    }
                }
                if (empty($itemVariant)) {
                    $json = json_decode($itemColor, true);
                    if (is_array($json)) {
                        $itemVariant = $json;
                    }
                }
                if (empty($itemVariant)) {
                    return strtolower(trim($itemColor)) === strtolower(trim($variant['color'] ?? ''));
                }
                $vColor = strtolower(trim($variant['color'] ?? '-'));
                $vSize = strtolower(trim($variant['size'] ?? '-'));
                $itemVColor = strtolower(trim($itemVariant['color'] ?? ($itemVariant['color_val'] ?? '-')));
                $itemVSize = strtolower(trim($itemVariant['size'] ?? ($itemVariant['size_val'] ?? '-')));
                if ($vColor === '') $vColor = '-';
                if ($vSize === '') $vSize = '-';
                if ($itemVColor === '') $itemVColor = '-';
                if ($itemVSize === '') $itemVSize = '-';
                return $vColor === $itemVColor && $vSize === $itemVSize;
            };

            $purchased = 0;
            foreach ($purchasesList as $pItem) {
                if ($matchSaleItemToVariant($pItem->color, $v)) {
                    $pUnit = strtolower(trim($pItem->unit ?? ''));
                    $itemPPB = (float) ($pItem->pieces_per_box > 0 ? $pItem->pieces_per_box : $vPpb);
                    if ($itemPPB <= 0) $itemPPB = 1;

                    if (in_array($pUnit, ['carton', 'ctn', 'box']) || $product->size_mode === 'by_cartons') {
                        if (isset($pItem->boxes_qty) && ($pItem->boxes_qty > 0 || $pItem->loose_qty > 0)) {
                            $pPieces = (((int) $pItem->boxes_qty) * $itemPPB) + ((int) $pItem->loose_qty);
                        } else {
                            [$b, $l] = \App\Http\Controllers\PurchaseController::parseCartonQty($pItem->qty);
                            $pPieces = ($b * $itemPPB) + $l;
                        }
                    } elseif (in_array($pUnit, ['gm', 'g'])) {
                        $pPieces = ((float) $pItem->qty) / 1000.0;
                    } else {
                        $pPieces = (float) $pItem->qty;
                    }
                    $purchased += $pPieces;
                }
            }

            $pReturned = 0;
            foreach ($purchaseReturnsList as $prItem) {
                if ($matchSaleItemToVariant($prItem->color, $v)) {
                    $pReturned += (float) $prItem->qty;
                }
            }

            $sold = 0;
            foreach ($salesList as $sItem) {
                if ($matchSaleItemToVariant($sItem->color, $v)) {
                    $sold += (float) $sItem->total_pieces;
                }
            }

            $returnedQty = 0;
            foreach ($returnsList as $rItem) {
                $rColor = $rItem->color;
                if (empty($rColor)) {
                    $saleColors = $saleItemsMap[$rItem->sale_id] ?? [];
                    $rColor = !empty($saleColors) ? $saleColors[0] : '';
                }
                if ($matchSaleItemToVariant($rColor, $v)) {
                    $returnedQty += (float) $rItem->qty;
                }
            }

            $vBalance = max(0, $initial + $purchased - $sold + $returnedQty - $pReturned);
            
            $totalCalculatedPieces += $vBalance;
        }
    } else {
        // No variants, calculate normally from stock_movements or just keep warehouse stock
        $totalCalculatedPieces = DB::table('stock_movements')->where('product_id', $product->id)->sum('qty');
    }

    if ($totalCalculatedPieces > 0 || count($parsedVariants) > 0) {
        $stock = WarehouseStock::firstOrNew([
            'product_id' => $product->id,
            'warehouse_id' => 1
        ]);
        
        // Only update if it's different to avoid unnecessary writes
        if (abs($stock->total_pieces - $totalCalculatedPieces) > 0.01) {
            $stock->total_pieces = $totalCalculatedPieces;
            
            $ppb = $product->pieces_per_box > 0 ? $product->pieces_per_box : 1;
            $stock->quantity = round($stock->total_pieces / $ppb, 2);
            $stock->save();
            $count++;
            echo "Updated Product {$product->id} ({$product->item_name}) stock to {$totalCalculatedPieces}\n";
        }
    }
}

echo "Updated $count products in WarehouseStock.\n";
