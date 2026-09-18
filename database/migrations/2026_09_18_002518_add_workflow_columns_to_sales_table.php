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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'sale_type')) {
                $table->enum('sale_type', ['direct_sale', 'quotation', 'sales_order'])->default('direct_sale')->after('sale_status');
            }
            if (!Schema::hasColumn('sales', 'delivery_status')) {
                $table->enum('delivery_status', ['pending', 'partial', 'delivered'])->default('pending')->after('sale_type');
            }
            if (!Schema::hasColumn('sales', 'parent_quotation_id')) {
                $table->unsignedBigInteger('parent_quotation_id')->nullable()->after('delivery_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'sale_type')) {
                $table->dropColumn('sale_type');
            }
            if (Schema::hasColumn('sales', 'delivery_status')) {
                $table->dropColumn('delivery_status');
            }
            if (Schema::hasColumn('sales', 'parent_quotation_id')) {
                $table->dropColumn('parent_quotation_id');
            }
        });
    }
};
