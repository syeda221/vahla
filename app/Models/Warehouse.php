<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class, 'creater_id');
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'product_warehouse')
                    ->withPivot('stock');
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class, 'warehouse_id');
    }

    public function scopeShops($query)
    {
        return $query->where('is_shop', 1);
    }

    public function scopeGodowns($query)
    {
        return $query->where('is_shop', 0);
    }

    public static function defaultShop()
    {
        return static::where('is_shop', 1)->first() ?? static::where('id', 1)->first();
    }


}
