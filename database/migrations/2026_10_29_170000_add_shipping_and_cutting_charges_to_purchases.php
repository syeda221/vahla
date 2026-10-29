<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'shipping_charges')) {
                $table->decimal('shipping_charges', 15, 2)->default(0)->after('extra_cost');
            }
            if (!Schema::hasColumn('purchases', 'cutting_charges')) {
                $table->decimal('cutting_charges', 15, 2)->default(0)->after('shipping_charges');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_items', 'effective_price')) {
                $table->decimal('effective_price', 15, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('purchase_items', 'allocated_extra_cost')) {
                $table->decimal('allocated_extra_cost', 15, 2)->default(0)->after('line_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'cutting_charges')) {
                $table->dropColumn('cutting_charges');
            }
            if (Schema::hasColumn('purchases', 'shipping_charges')) {
                $table->dropColumn('shipping_charges');
            }
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_items', 'allocated_extra_cost')) {
                $table->dropColumn('allocated_extra_cost');
            }
            if (Schema::hasColumn('purchase_items', 'effective_price')) {
                $table->dropColumn('effective_price');
            }
        });
    }
};
