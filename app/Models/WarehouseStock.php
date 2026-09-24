<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'quantity',
        'total_pieces',
        'remarks',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    // App\Models\WarehouseStock.php
    //  Rename relation
    public function stockWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    // public function product() {
    //     return $this->belongsTo(Product::class);
    // }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'warehouse_stocks', 'warehouse_id', 'product_id')
            ->withPivot('quantity', 'price', 'remarks');
    }

    protected static function booted()
    {
        static::saved(function ($warehouseStock) {
            $product = $warehouseStock->product;
            if ($product && !is_null($product->alert_quantity)) {
                $totalPieces = self::where('product_id', $product->id)->sum('total_pieces');
                if ($totalPieces < $product->alert_quantity) {
                    \App\Models\SystemNotification::createStockAlertNotification($product, $totalPieces);
                }
            }
        });
    }
}
