<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCodeSeries extends Model
{
    use HasFactory;

    protected $fillable = ['prefix', 'next_number', 'is_default'];
}
