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
        if (Schema::hasTable('product_set_items')) {
            Schema::table('product_set_items', function (Blueprint $table) {
                if (!Schema::hasColumn('product_set_items', 'variant_key')) {
                    $table->string('variant_key')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('product_set_items', 'variant_unit')) {
                    $table->string('variant_unit', 50)->nullable()->after('variant_key');
                }
                if (!Schema::hasColumn('product_set_items', 'variant_name')) {
                    $table->string('variant_name')->nullable()->after('variant_unit');
                }
                if (!Schema::hasColumn('product_set_items', 'weight_per_piece')) {
                    $table->decimal('weight_per_piece', 12, 4)->nullable()->after('variant_name');
                }
                if (!Schema::hasColumn('product_set_items', 'conv_factor')) {
                    $table->decimal('conv_factor', 14, 6)->nullable()->after('weight_per_piece');
                }
                if (!Schema::hasColumn('product_set_items', 'sale_price')) {
                    $table->decimal('sale_price', 15, 2)->nullable()->after('conv_factor');
                }
                if (!Schema::hasColumn('product_set_items', 'variant_data')) {
                    $table->longText('variant_data')->nullable()->after('sale_price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_set_items')) {
            Schema::table('product_set_items', function (Blueprint $table) {
                $columns = ['variant_key', 'variant_unit', 'variant_name', 'weight_per_piece', 'conv_factor', 'sale_price', 'variant_data'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('product_set_items', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
