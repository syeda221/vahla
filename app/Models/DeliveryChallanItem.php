<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallanItem extends Model
{
    protected $table = 'delivery_challan_items';

    protected $fillable = [
        'delivery_challan_id',
        'sale_item_id',
        'product_id',
        'warehouse_id',
        'delivered_qty',
        'boxes',
        'loose_pieces'
    ];

    public function deliveryChallan()
    {
        return $this->belongsTo(DeliveryChallan::class, 'delivery_challan_id');
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class, 'sale_item_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
