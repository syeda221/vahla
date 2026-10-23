<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLink extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The parent product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * The linked / recommended product.
     */
    public function linkedProduct()
    {
        return $this->belongsTo(Product::class, 'linked_product_id');
    }
}
