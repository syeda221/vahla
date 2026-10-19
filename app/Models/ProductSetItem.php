<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSetItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function productSet()
    {
        return $this->belongsTo(ProductSet::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
