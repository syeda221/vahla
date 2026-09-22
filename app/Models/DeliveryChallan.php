<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{
    protected $table = 'delivery_challans';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'dc_number',
        'dc_date',
        'status',
        'is_invoiced',
        'invoice_id',
        'remarks',
        'created_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(Sale::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function getCustomerRecordAttribute()
    {
        return $this->customer ?: optional($this->sale)->customer_relation;
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryChallanItem::class, 'delivery_challan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
