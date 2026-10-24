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
        if (!Schema::hasColumn('warehouses', 'is_shop')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->boolean('is_shop')->default(0)->after('warehouse_name');
            });

            // Mark id 1 as Main Shop / Outlet by default
            DB::table('warehouses')->where('id', 1)->update(['is_shop' => 1]);
        }

        if (!Schema::hasColumn('sales', 'warehouse_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->default(1)->after('customer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('sales', 'warehouse_id')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('warehouse_id');
            });
        }

        if (Schema::hasColumn('warehouses', 'is_shop')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropColumn('is_shop');
            });
        }
    }
};
