<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleFieldConfiguration extends Model
{
    protected $guarded = [];

    /**
     * Check if a field is visible in print.
     */
    public static function isVisibleInPrint(string $field, bool $default = true): bool
    {
        return $default;
    }

    /**
     * Check if a field is enabled.
     */
    public static function isEnabled(string $field, bool $default = true): bool
    {
        return $default;
    }
}
