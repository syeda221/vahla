<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('purchase_items') && Schema::hasColumn('purchase_items', 'qty')) {
            DB::statement('ALTER TABLE purchase_items MODIFY qty DECIMAL(12, 4) DEFAULT 0');
        }

        if (Schema::hasTable('purchase_return_items') && Schema::hasColumn('purchase_return_items', 'qty')) {
            DB::statement('ALTER TABLE purchase_return_items MODIFY qty DECIMAL(12, 4) DEFAULT 0');
        }

        if (Schema::hasTable('inward_gatepass_items') && Schema::hasColumn('inward_gatepass_items', 'qty')) {
            DB::statement('ALTER TABLE inward_gatepass_items MODIFY qty DECIMAL(12, 4) DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchase_items') && Schema::hasColumn('purchase_items', 'qty')) {
            DB::statement('ALTER TABLE purchase_items MODIFY qty INT DEFAULT 0');
        }

        if (Schema::hasTable('purchase_return_items') && Schema::hasColumn('purchase_return_items', 'qty')) {
            DB::statement('ALTER TABLE purchase_return_items MODIFY qty INT DEFAULT 0');
        }

        if (Schema::hasTable('inward_gatepass_items') && Schema::hasColumn('inward_gatepass_items', 'qty')) {
            DB::statement('ALTER TABLE inward_gatepass_items MODIFY qty INT DEFAULT 0');
        }
    }
};
