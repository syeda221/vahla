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
        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_id')->nullable()->change();
            $table->unsignedBigInteger('customer_id')->nullable()->after('sale_id');
            $table->boolean('is_invoiced')->default(0)->after('status');
        });

        Schema::table('delivery_challan_items', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_item_id')->nullable()->change();
            $table->decimal('price', 15, 4)->default(0)->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_challans', function (Blueprint $table) {
            // Note: Can't easily revert nullable()->change() without knowing original state,
            // but we can drop the new columns.
            $table->dropColumn(['customer_id', 'is_invoiced']);
        });

        Schema::table('delivery_challan_items', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
