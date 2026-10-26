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
        Schema::table('sale_return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_return_items', 'product_set_id')) {
                $table->unsignedBigInteger('product_set_id')->nullable()->after('product_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_return_items', function (Blueprint $table) {
            if (Schema::hasColumn('sale_return_items', 'product_set_id')) {
                $table->dropColumn('product_set_id');
            }
        });
    }
};
