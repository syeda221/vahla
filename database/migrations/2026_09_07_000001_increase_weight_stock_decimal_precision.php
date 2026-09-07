<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kg products use tiny quantities (e.g. conv_factor = 0.00000259 kg/piece).
     * decimal(12,4) / decimal(12,3) columns round these to 0.0000, so 1 piece
     * sales never deduct from stock. Bump precision to 8 decimal places everywhere
     * weight/quantity is stored.
     */
    public function up(): void
    {
        $changes = [
            ['warehouse_stocks', 'total_pieces'],
            ['warehouse_stocks', 'quantity'],
            ['sale_items', 'total_pieces'],
            ['sale_items', 'qty'],
            ['stock_movements', 'qty'],
            ['sale_return_items', 'qty'],
            ['sale_return_items', 'boxes'],
            ['purchase_items', 'qty'],
            ['purchase_return_items', 'qty'],
            ['inward_gatepass_items', 'qty'],
        ];

        foreach ($changes as [$table, $col]) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $col)) {
                DB::statement("ALTER TABLE {$table} MODIFY {$col} DECIMAL(20, 8) DEFAULT 0");
            }
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'weight_per_piece')) {
            DB::statement('ALTER TABLE products MODIFY weight_per_piece DECIMAL(14, 6) DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $restores = [
            ['warehouse_stocks', 'total_pieces', 'DECIMAL(12, 4)'],
            ['warehouse_stocks', 'quantity', 'INT'],
            ['sale_items', 'total_pieces', 'DECIMAL(12, 4)'],
            ['sale_items', 'qty', 'DECIMAL(12, 4)'],
            ['stock_movements', 'qty', 'DECIMAL(12, 3)'],
            ['sale_return_items', 'qty', 'DECIMAL(15, 2)'],
            ['sale_return_items', 'boxes', 'DECIMAL(15, 2)'],
            ['purchase_items', 'qty', 'DECIMAL(12, 4)'],
            ['purchase_return_items', 'qty', 'DECIMAL(12, 4)'],
            ['inward_gatepass_items', 'qty', 'DECIMAL(12, 4)'],
        ];

        foreach ($restores as [$table, $col, $type]) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $col)) {
                DB::statement("ALTER TABLE {$table} MODIFY {$col} {$type} DEFAULT 0");
            }
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'weight_per_piece')) {
            DB::statement('ALTER TABLE products MODIFY weight_per_piece DECIMAL(12, 4) DEFAULT 0');
        }
    }
};