<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'warehouse_id', 'warehouse_breakdown', 'product_id', 'product_set_id',
        'brand_id', 'category_id', 'sub_category_id', 'unit_id',
        'qty', 'price', 'total',
        'discount_percent', 'discount_amount', 'discount_type',
        'color', 'description', 'total_pieces', 'loose_pieces',
        'price_per_piece', 'price_per_m2', 'delivered_qty'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSet()
    {
        return $this->belongsTo(\App\Models\ProductSet::class, 'product_set_id');
    }
    
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getRemainingQtyAttribute()
    {
        return max(0, $this->total_pieces - $this->delivered_qty);
    }
}
