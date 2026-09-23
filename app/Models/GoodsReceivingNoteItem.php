<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivingNoteItem extends Model
{
    protected $table = 'goods_receiving_note_items';

    protected $fillable = [
        'goods_receiving_note_id',
        'purchase_item_id',
        'product_id',
        'warehouse_id',
        'received_qty',
        'boxes',
        'loose_pieces',
        'color',
        'purchase_price',
    ];

    protected $casts = [
        'received_qty' => 'decimal:2',
        'boxes' => 'decimal:2',
        'loose_pieces' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    public function goodsReceivingNote()
    {
        return $this->belongsTo(GoodsReceivingNote::class, 'goods_receiving_note_id');
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class, 'purchase_item_id');
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
