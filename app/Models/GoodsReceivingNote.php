<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceivingNote extends Model
{
    protected $table = 'goods_receiving_notes';

    protected $fillable = [
        'purchase_id',
        'vendor_id',
        'warehouse_id',
        'grn_number',
        'grn_date',
        'status',
        'is_invoiced',
        'invoice_id',
        'remarks',
        'carrier_info',
        'created_by',
    ];

    protected $casts = [
        'grn_date' => 'date',
        'is_invoiced' => 'boolean',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Purchase::class, 'invoice_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(GoodsReceivingNoteItem::class, 'goods_receiving_note_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
