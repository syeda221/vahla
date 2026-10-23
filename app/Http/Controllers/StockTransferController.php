<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with('fromWarehouse', 'toWarehouse', 'product')
            ->latest()
            ->get();

        return view('admin_panel.warehouses.stock_transfers.index', compact('transfers'));
    }

    public function create()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        return view('admin_panel.warehouses.stock_transfers.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_warehouse_id' => 'required|integer|exists:warehouses,id',
            'to_warehouse_id'   => 'nullable|integer|exists:warehouses,id',
            'to_shop'           => 'nullable|boolean',
            'product_id'        => 'required|array',
            'product_id.*'      => 'required|integer|exists:products,id',
            'quantity'          => 'required|array',
            'quantity.*'        => 'required|numeric|min:1',
            'variant_key'       => 'nullable|array',
            'variant_name'      => 'nullable|array',
            'remarks'           => 'nullable|string',
        ]);

        $fromWarehouse = (int) $request->from_warehouse_id;
        $toShop        = $request->boolean('to_shop');
        $toWarehouse   = $toShop ? null : ($request->to_warehouse_id ? (int) $request->to_warehouse_id : null);
        $remarks       = $request->remarks;

        if (! $toShop && ! $toWarehouse) {
            return back()->with('error', 'Please select a destination warehouse or choose Transfer to Shop.')->withInput();
        }

        if (! $toShop && $fromWarehouse === $toWarehouse) {
            return back()->with('error', 'Source and Destination warehouses cannot be the same.')->withInput();
        }

        // Build items list preserving variant info (do NOT merge same product different variants)
        $items = [];
        foreach ($request->product_id as $index => $pid) {
            $qty = (float) ($request->quantity[$index] ?? 0);
            if ($qty <= 0) continue;

            $vKey  = $request->variant_key[$index]  ?? '';
            $vName = $request->variant_name[$index]  ?? '';

            $product = Product::find($pid);
            if (! $product) continue;

            $ppb          = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
            $isCartonMode = in_array($product->size_mode, ['by_cartons', 'by_bandal']);

            if (! empty($vKey) || ! empty($vName)) {
                $decodedVariants = json_decode($product->color, true) ?: [];
                foreach ($decodedVariants as $dv) {
                    $key = ($dv['name'] ?? '').'|'.($dv['size'] ?? '-').'|'.($dv['color'] ?? '-');
                    if ($key === $vKey || ($dv['name'] ?? '') === $vName) {
                        if (! empty($dv['conv_factor']) && (float) $dv['conv_factor'] > 0) {
                            $ppb = (float) $dv['conv_factor'];
                        }
                        break;
                    }
                }
            }

            $pieces = ($isCartonMode && $ppb > 1) ? ($qty * $ppb) : $qty;
            $boxes  = ($isCartonMode && $ppb > 1) ? $qty : (($ppb > 1) ? round($qty / $ppb, 2) : $qty);

            $items[] = [
                'product_id'   => (int) $pid,
                'quantity'     => $qty,
                'pieces'       => $pieces,
                'boxes'        => $boxes,
                'ppb'          => $ppb,
                'variant_key'  => $vKey,
                'variant_name' => $vName,
            ];
        }

        if (empty($items)) {
            return back()->with('error', 'Please select at least one product with quantity greater than 0.')->withInput();
        }

        try {
            DB::transaction(function () use ($fromWarehouse, $toWarehouse, $toShop, $remarks, $items) {

                // Aggregate total boxes and pieces per product for warehouse_stocks update
                $productTotals = [];
                foreach ($items as $item) {
                    $pid = $item['product_id'];
                    if (! isset($productTotals[$pid])) {
                        $productTotals[$pid] = ['boxes' => 0, 'pieces' => 0];
                    }
                    $productTotals[$pid]['boxes']  += $item['boxes'];
                    $productTotals[$pid]['pieces'] += $item['pieces'];
                }

                // Lock & validate source stock per product
                $sourceStocks = [];
                foreach ($productTotals as $productId => $tot) {
                    $sourceStock = WarehouseStock::where('warehouse_id', $fromWarehouse)
                        ->where('product_id', $productId)
                        ->lockForUpdate()
                        ->first();

                    $product     = Product::find($productId);
                    $productName = $product ? $product->item_name : 'Product #'.$productId;

                    $availQty    = $sourceStock ? (int) $sourceStock->quantity : 0;
                    $availPieces = $sourceStock ? (float) $sourceStock->total_pieces : 0;

                    if (! $sourceStock || ($availPieces < $tot['pieces'] && $availQty < $tot['boxes'])) {
                        throw new \Exception("Insufficient stock for {$productName}. Requested: {$tot['boxes']}, Available: {$availQty}");
                    }

                    $sourceStocks[$productId] = $sourceStock;
                }

                // Apply stock changes per product
                foreach ($productTotals as $productId => $tot) {
                    $sourceStock = $sourceStocks[$productId];

                    // Reduce source warehouse stock
                    $sourceStock->quantity     = max(0, $sourceStock->quantity - $tot['boxes']);
                    $sourceStock->total_pieces = max(0, (float) $sourceStock->total_pieces - $tot['pieces']);
                    $sourceStock->save();

                    // Increase destination warehouse stock
                    if (! $toShop && $toWarehouse) {
                        $destStock = WarehouseStock::firstOrNew([
                            'warehouse_id' => $toWarehouse,
                            'product_id'   => $productId,
                        ]);
                        if (! $destStock->exists) {
                            $destStock->quantity     = 0;
                            $destStock->total_pieces = 0;
                        }
                        $destStock->quantity     += $tot['boxes'];
                        $destStock->total_pieces += $tot['pieces'];
                        $destStock->save();
                    }
                }

                // Record one StockTransfer row per item (preserving variant info)
                foreach ($items as $item) {
                    $productId        = $item['product_id'];
                    $totalQty         = $item['quantity'];
                    $piecesToTransfer = $item['pieces'];

                    $transfer = StockTransfer::create([
                        'from_warehouse_id' => $fromWarehouse,
                        'to_warehouse_id'   => $toShop ? null : $toWarehouse,
                        'to_shop'           => $toShop ? 1 : 0,
                        'product_id'        => $productId,
                        'quantity'          => $totalQty,
                        'variant_key'       => $item['variant_key']  ?: null,
                        'variant_name'      => $item['variant_name'] ?: null,
                        'remarks'           => $remarks,
                    ]);

                    // Movement Log (OUT)
                    DB::table('stock_movements')->insert([
                        'product_id' => $productId,
                        'type'       => 'out',
                        'qty'        => -1 * abs($piecesToTransfer),
                        'ref_type'   => 'TRANSFER_OUT',
                        'ref_id'     => $transfer->id,
                        'note'       => 'Transfer to '.($toShop ? 'Shop' : "Warehouse #{$toWarehouse}"),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Movement Log (IN)
                    if (! $toShop && $toWarehouse) {
                        DB::table('stock_movements')->insert([
                            'product_id' => $productId,
                            'type'       => 'in',
                            'qty'        => abs($piecesToTransfer),
                            'ref_type'   => 'TRANSFER_IN',
                            'ref_id'     => $transfer->id,
                            'note'       => "Transfer from Warehouse #{$fromWarehouse}",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Transfer Failed: '.$e->getMessage())->withInput();
        }

        return redirect()->route('stock_transfers.index')->with('success', 'Stock transferred successfully.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        return back()->with('error', 'Deleting transfers is not allowed.');
    }

    public function getStockQuantity(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $productId = $request->product_id;

        $stock = WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->first();

        $product = Product::find($productId);
        $ppb = ($product && $product->pieces_per_box > 0) ? (float) $product->pieces_per_box : 1;

        $quantity = $stock ? (int) $stock->quantity : 0;
        $totalPieces = $stock ? (float) $stock->total_pieces : 0;

        return response()->json([
            'quantity' => $quantity,
            'total_pieces' => $totalPieces,
            'ppb' => $ppb,
            'item_name' => $product ? $product->item_name : '',
        ]);
    }

    public function getProductsByWarehouse(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        if (! $warehouseId) {
            return response()->json([]);
        }

        // Products that have any stock in this warehouse
        $warehouseStocks = DB::table('warehouse_stocks')
            ->where('warehouse_id', $warehouseId)
            ->where(function ($q) {
                $q->where('quantity', '>', 0)->orWhere('total_pieces', '>', 0);
            })
            ->get()
            ->keyBy('product_id');

        if ($warehouseStocks->isEmpty()) {
            return response()->json([]);
        }

        $productIds = $warehouseStocks->keys()->toArray();

        // Load full product records
        $products = Product::whereIn('id', $productIds)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('id');

        // ── Bulk-load all movement data for these products in this warehouse ──

        $openingStocks = DB::table('opening_stocks')
            ->where('warehouse_id', $warehouseId)
            ->whereIn('product_id', $productIds)
            ->get();

        $saleItems = DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.sale_status', '!=', 'cancelled')
            ->where('sale_items.warehouse_id', $warehouseId)
            ->whereIn('sale_items.product_id', $productIds)
            ->select('sale_items.product_id', 'sale_items.total_pieces', 'sale_items.color')
            ->get();

        $saleReturnItems = DB::table('sale_return_items')
            ->where('warehouse_id', $warehouseId)
            ->whereIn('product_id', $productIds)
            ->select('product_id', 'qty', 'boxes', 'loose_pieces', 'color')
            ->get();

        $purchaseItems = DB::table('purchase_items')
            ->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->whereNull('purchases.deleted_at')
            ->where('purchases.warehouse_id', $warehouseId)
            ->whereIn('purchase_items.product_id', $productIds)
            ->select(
                'purchase_items.product_id',
                'purchase_items.qty',
                'purchase_items.boxes_qty',
                'purchase_items.loose_qty',
                'purchase_items.pieces_per_box',
                'purchase_items.color'
            )
            ->get();

        $transfersOut = DB::table('stock_transfers')
            ->where('from_warehouse_id', $warehouseId)
            ->whereIn('product_id', $productIds)
            ->get(['product_id', 'quantity', 'variant_key', 'variant_name']);

        $transfersIn = DB::table('stock_transfers')
            ->where('to_warehouse_id', $warehouseId)
            ->where('to_shop', false)
            ->whereIn('product_id', $productIds)
            ->get(['product_id', 'quantity', 'variant_key', 'variant_name']);

        $stockAdjustments = DB::table('stock_adjustments')
            ->where('warehouse_id', $warehouseId)
            ->whereIn('product_id', $productIds)
            ->get(['product_id', 'variant_key', 'variant_name', 'old_stock', 'new_stock']);

        // ── Build result ───────────────────────────────────────────────────────
        $result = [];

        foreach ($warehouseStocks as $productId => $ws) {
            $product = $products->get($productId);
            if (! $product) continue;

            $ppb          = (float) ($product->pieces_per_box > 0 ? $product->pieces_per_box : 1);
            $isCartonMode = in_array($product->size_mode, ['by_cartons', 'by_bandal']);

            // Parse variants from the JSON color column
            $variants = [];
            if (! empty($product->color)) {
                $decoded = json_decode($product->color, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    $variants = $decoded;
                }
            }

            $productOpenings = $openingStocks->where('product_id', $productId);
            $hasOpeningData  = $productOpenings->isNotEmpty();

            // ── Single / no real variant ───────────────────────────────────
            if (count($variants) <= 1) {
                $livePieces = (float) $ws->total_pieces;
                $liveQty    = (float) $ws->quantity;

                // Format stock display for single-variant products too
                if ($isCartonMode && $ppb > 1) {
                    $sBoxes       = (int) floor($livePieces / $ppb);
                    $sLoose       = (int) round($livePieces - ($sBoxes * $ppb));
                    $displayStock = $sLoose > 0 ? "{$sBoxes}.{$sLoose}" : (string) $sBoxes;
                    $maxQty       = $sBoxes > 0 ? $sBoxes : (int) $liveQty;
                } else {
                    $displayStock = (string) (int) $livePieces;
                    $maxQty       = (int) $livePieces;
                }

                $vName = ! empty($variants) ? ($variants[0]['name'] ?? '') : '';
                $vKey  = ! empty($variants)
                    ? ($vName.'|'.($variants[0]['size'] ?? '-').'|'.($variants[0]['color'] ?? '-'))
                    : '';

                $result[] = [
                    'id'              => (int) $productId,
                    'item_name'       => $product->item_name,
                    'item_code'       => $product->item_code,
                    'size_mode'       => $product->size_mode,
                    'pieces_per_box'  => $ppb,
                    'available_qty'   => $maxQty,
                    'available_pieces'=> $livePieces,
                    'display_stock'   => $displayStock,
                    'variant_key'     => $vKey,
                    'variant_name'    => $vName,
                    'display_name'    => $product->item_name,
                ];
                continue;
            }

            // ── Multiple variants: calculate actual live stock per variant ──
            $variantsAdded = 0;
            foreach ($variants as $v) {
                $vName  = $v['name'] ?? $product->item_name;
                $vSize  = $v['size']  ?? '-';
                $vColor = $v['color'] ?? '-';
                $vKey   = $vName.'|'.$vSize.'|'.$vColor;
                $vPpb   = (float) ($v['conv_factor'] ?? $ppb);
                if ($vPpb <= 0) $vPpb = $ppb;

                // Opening stock for this variant
                $matchOp  = $productOpenings->first(function ($op) use ($vKey, $vName) {
                    return ($op->variant_key === $vKey) || ($op->variant_name === $vName);
                });
                $opPieces = $matchOp ? (float) $matchOp->total_pieces : 0;

                // Sales
                $soldPieces = 0;
                foreach ($saleItems->where('product_id', $productId) as $si) {
                    if ($this->matchVariantColor($si->color, $vKey, $vName)) {
                        $soldPieces += (float) $si->total_pieces;
                    }
                }

                // Sale returns
                $returnPieces = 0;
                foreach ($saleReturnItems->where('product_id', $productId) as $ri) {
                    if ($this->matchVariantColor($ri->color, $vKey, $vName)) {
                        $rp = (float) ($ri->qty > 0
                            ? $ri->qty
                            : (((float) $ri->boxes * $vPpb) + (float) $ri->loose_pieces));
                        $returnPieces += $rp;
                    }
                }

                // Purchases
                $purchasedPieces = 0;
                foreach ($purchaseItems->where('product_id', $productId) as $pi) {
                    if ($this->matchVariantColor($pi->color, $vKey, $vName)) {
                        $piPpb   = (float) ($pi->pieces_per_box > 0 ? $pi->pieces_per_box : $vPpb);
                        $pPieces = ((float) $pi->boxes_qty * $piPpb) + (float) $pi->loose_qty;
                        if ($pPieces <= 0) $pPieces = (float) $pi->qty;
                        $purchasedPieces += $pPieces;
                    }
                }

                // Transfer out / in
                $tOutPieces = 0;
                foreach ($transfersOut->where('product_id', $productId) as $t) {
                    if (empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName) {
                        $tOutPieces += (float) $t->quantity * $vPpb;
                    }
                }
                $tInPieces = 0;
                foreach ($transfersIn->where('product_id', $productId) as $t) {
                    if (empty($t->variant_key) || $t->variant_key === $vKey || $t->variant_name === $vName) {
                        $tInPieces += (float) $t->quantity * $vPpb;
                    }
                }

                $adjPieces = 0;
                foreach ($stockAdjustments->where('product_id', $productId) as $adj) {
                    if (empty($adj->variant_key) || $adj->variant_key === $vKey || $adj->variant_name === $vName) {
                        $adjPieces += ((float)$adj->new_stock - (float)$adj->old_stock);
                    }
                }

                // Live pieces for this variant
                $hasMovements = ($opPieces > 0 || $purchasedPieces > 0 || $soldPieces > 0 || $returnPieces > 0 || $tInPieces > 0 || $tOutPieces > 0 || $adjPieces != 0);
                if ($hasMovements) {
                    $livePieces = max(0, $opPieces + $purchasedPieces - $soldPieces + $returnPieces + $tInPieces - $tOutPieces + $adjPieces);
                } else {
                    $livePieces = 0;
                }

                // Skip variants with zero live stock
                if ($livePieces <= 0) continue;

                $variantsAdded++;

                // Format display stock: carton mode → "boxes.loose" (e.g. 8.1 = 8 cartons + 1 loose piece)
                if ($isCartonMode && $vPpb > 1) {
                    $liveBoxes    = (int) floor($livePieces / $vPpb);
                    $liveLoose    = (int) round($livePieces - ($liveBoxes * $vPpb));
                    $displayStock = $liveLoose > 0 ? "{$liveBoxes}.{$liveLoose}" : (string) $liveBoxes;
                    $maxQty       = $liveBoxes; // transfer qty entered in boxes
                } else {
                    $displayStock = (string) (int) $livePieces;
                    $maxQty       = (int) $livePieces;
                }

                // Build display label
                $label = $product->item_name;
                if ($vName && $vName !== $product->item_name) $label .= ' – '.$vName;
                if ($vSize && $vSize !== '-') $label .= ' ('.$vSize.')';
                if ($vColor && $vColor !== '-') $label .= ' ['.$vColor.']';

                $result[] = [
                    'id'              => (int) $productId,
                    'item_name'       => $product->item_name,
                    'item_code'       => $product->item_code,
                    'size_mode'       => $product->size_mode,
                    'pieces_per_box'  => $vPpb,
                    'available_qty'   => $maxQty,        // integer boxes/pieces — used for max qty validation
                    'available_pieces'=> $livePieces,
                    'display_stock'   => $displayStock,  // formatted "8.1" — shown in the Stock cell
                    'variant_key'     => $vKey,
                    'variant_name'    => $vName,
                    'display_name'    => $label,
                ];
            }

            // Fallback: if multi-variant product has warehouse_stocks but no individual variant movements were found
            if ($variantsAdded === 0 && (float) $ws->total_pieces > 0) {
                $livePieces = (float) $ws->total_pieces;
                $liveQty    = (float) $ws->quantity;
                if ($isCartonMode && $ppb > 1) {
                    $sBoxes       = (int) floor($livePieces / $ppb);
                    $sLoose       = (int) round($livePieces - ($sBoxes * $ppb));
                    $displayStock = $sLoose > 0 ? "{$sBoxes}.{$sLoose}" : (string) $sBoxes;
                    $maxQty       = $sBoxes > 0 ? $sBoxes : (int) $liveQty;
                } else {
                    $displayStock = (string) (int) $livePieces;
                    $maxQty       = (int) $livePieces;
                }
                $vName = $variants[0]['name'] ?? '';
                $vKey  = $vName.'|'.($variants[0]['size'] ?? '-').'|'.($variants[0]['color'] ?? '-');
                $result[] = [
                    'id'              => (int) $productId,
                    'item_name'       => $product->item_name,
                    'item_code'       => $product->item_code,
                    'size_mode'       => $product->size_mode,
                    'pieces_per_box'  => $ppb,
                    'available_qty'   => $maxQty,
                    'available_pieces'=> $livePieces,
                    'display_stock'   => $displayStock,
                    'variant_key'     => $vKey,
                    'variant_name'    => $vName,
                    'display_name'    => $product->item_name . ($vName ? ' – '.$vName : ''),
                ];
            }
        }

        return response()->json($result);
    }

    /**
     * Decode the encoded color/variant JSON blob stored against sale/purchase items
     * and check if it matches the given variant key or name.
     */
    private function matchVariantColor(?string $rawColor, string $targetKey, string $targetName): bool
    {
        if (empty($rawColor)) return false;

        $raw = $rawColor;
        // Try base64 decode first (some rows are base64-encoded JSON)
        $b64 = base64_decode($raw, true);
        if ($b64 !== false && json_decode($b64) !== null) {
            $raw = $b64;
        }

        $arr = json_decode($raw, true);
        if (! is_array($arr)) return false;

        $name = $arr['name'] ?? null;
        if ($targetName && $name === $targetName) return true;

        if ($targetKey) {
            $k = ($arr['name'] ?? '').'|'.($arr['size'] ?? '-').'|'.($arr['color'] ?? '-');
            if ($k === $targetKey) return true;
        }

        return false;
    }
}
