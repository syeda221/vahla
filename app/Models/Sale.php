<?php

// app/Models/Sale.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'customer_id', 'reference', 'total_amount_Words', 'total_bill_amount',
        'total_extradiscount', 'total_net', 'cash', 'card', 'change', 'change_account_id',
        'total_items', 'discount_type', 'sale_status', 'invoice_no', 'is_booking',
        'sale_type', 'delivery_status', 'parent_quotation_id'
    ];

    public function customer_relation()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function change_account()
    {
        return $this->belongsTo(Account::class, 'change_account_id', 'id');
    }

    public function product_relation()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }

    public function getIsWalkinAttribute()
    {
        return empty($this->customer_id) || !empty($this->walkin_name);
    }

    public static function generateInvoiceNo($prefix = null)
    {
        return \App\Models\InvoiceSeries::generateNextNo($prefix);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'source');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class, 'sale_id');
    }

    public function deliveryChallans()
    {
        return $this->hasMany(DeliveryChallan::class, 'sale_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Sale::class, 'parent_quotation_id');
    }

    public function recalculateDeliveryStatus()
    {
        $this->load(['items', 'deliveryChallans.items']);

        if ($this->items->isEmpty()) {
            return;
        }

        $allDelivered = true;
        $anyDelivered = false;

        foreach ($this->items as $item) {
            $deliveredQty = \App\Models\DeliveryChallanItem::whereHas('deliveryChallan', function ($q) {
                $q->where('sale_id', $this->id);
            })
            ->where(function ($q) use ($item) {
                $q->where('sale_item_id', $item->id)
                  ->orWhere(function ($q2) use ($item) {
                      $q2->whereNull('sale_item_id')
                         ->where('product_id', $item->product_id);
                  });
            })
            ->sum('delivered_qty');

            $item->delivered_qty = (float) $deliveredQty;
            $item->save();

            $targetQty = (float) ($item->total_pieces > 0 ? $item->total_pieces : $item->qty);

            if (($targetQty - $item->delivered_qty) > 0.0001) {
                $allDelivered = false;
            }

            if ($item->delivered_qty > 0.0001) {
                $anyDelivered = true;
            }
        }

        if ($allDelivered && $anyDelivered) {
            $this->delivery_status = 'delivered';
        } elseif ($anyDelivered) {
            $this->delivery_status = 'partial';
        } else {
            $this->delivery_status = 'pending';
        }

        $this->save();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!isset($model->is_synced)) {
                $model->is_synced = 0;
            }
        });
    }
}
