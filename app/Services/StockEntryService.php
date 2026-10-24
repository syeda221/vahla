<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockEntryService
{
    /**
     * Add stock to a warehouse.
     *
     * @return WarehouseStock
     *
     * @throws Exception
     */
    public function addStock(int $warehouseId, int $productId, float $totalPieces, float $totalBox = 0, ?string $remarks = null)
    {
        return DB::transaction(function () use ($warehouseId, $productId, $totalPieces, $totalBox, $remarks) {
            // 1. Validation
            $warehouse = Warehouse::findOrFail($warehouseId);
            $product = Product::findOrFail($productId);

            if ($totalPieces < 0) {
                throw new \InvalidArgumentException('Quantity must be non-negative.');
            }

            // 2. Get or Create Warehouse Stock Record
            $stock = WarehouseStock::firstOrNew([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
            ]);

            // Initialize if new
            if (! $stock->exists) {
                $stock->quantity = 0;
                $stock->total_pieces = 0;
            }

            // 3. Update Stock
            $previousPieces = (float)($stock->total_pieces ?? 0);
            $newPieces = $previousPieces + $totalPieces;

            $ppb = ($product->pieces_per_box > 0) ? (float)$product->pieces_per_box : 1;
            $newBox = round($newPieces / $ppb, 2);

            $stock->total_pieces = $newPieces;
            $stock->quantity = $newBox;
            if ($remarks) {
                $stock->remarks = $remarks;
            }
            $stock->save();

            // 4. Log Movement for Opening/Manual Stock Entry
            DB::table('stock_movements')->insert([
                'product_id' => $productId,
                'type' => 'in',
                'qty' => $totalPieces,
                'ref_type' => 'OPENING_STOCK',
                'ref_id' => $stock->id,
                'note' => $remarks ?: "Opening / Manual Stock Addition ({$warehouse->warehouse_name})",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $stock;
        });
    }
}
