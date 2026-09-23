<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'purchase_date' => 'date',
        'subtotal'      => 'decimal:2',
        'discount'      => 'decimal:2',
        'additional_discount' => 'decimal:2',
        'extra_cost'    => 'decimal:2',
        'net_amount'    => 'decimal:2',
        'paid_amount'   => 'decimal:2',
        'due_amount'    => 'decimal:2',
    ];

    public function branch()   { return $this->belongsTo(Branch::class); }
    public function warehouse(){ return $this->belongsTo(Warehouse::class); }
    public function vendor()   { return $this->belongsTo(Vendor::class, 'vendor_id'); }
    public function items()    { return $this->hasMany(PurchaseItem::class); }
    public function returns()  { return $this->hasMany(PurchaseReturn::class); }

    public function goodsReceivingNotes()
    {
        return $this->hasMany(GoodsReceivingNote::class, 'purchase_id');
    }

    public function parentOrder()
    {
        return $this->belongsTo(Purchase::class, 'parent_po_id');
    }

    public function childInvoices()
    {
        return $this->hasMany(Purchase::class, 'parent_po_id');
    }

    public function recalculateReceivingStatus(): void
    {
        if ($this->purchase_type !== 'purchase_order') {
            return;
        }

        $items = $this->items()->get();
        if ($items->isEmpty()) {
            $this->receiving_status = 'pending';
            $this->save();
            return;
        }

        $allReceived = true;
        $anyReceived = false;

        foreach ($items as $item) {
            $orderQty = (float) $item->qty;
            $receivedQty = (float) $item->received_qty;

            if ($receivedQty > 0) {
                $anyReceived = true;
            }
            if ($receivedQty < $orderQty) {
                $allReceived = false;
            }
        }

        if ($allReceived) {
            $this->receiving_status = 'received';
        } elseif ($anyReceived) {
            $this->receiving_status = 'partial';
        } else {
            $this->receiving_status = 'pending';
        }

        $this->save();
    }
}
