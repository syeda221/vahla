<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{
    protected $table = 'delivery_challans';

    protected $fillable = [
        'sale_id',
        'dc_number',
        'dc_date',
        'status',
        'remarks',
        'created_by',
    ];

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
