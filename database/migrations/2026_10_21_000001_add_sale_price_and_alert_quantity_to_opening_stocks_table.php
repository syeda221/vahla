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
        Schema::table('opening_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('opening_stocks', 'sale_price')) {
                $table->decimal('sale_price', 14, 2)->nullable()->after('cost_price');
            }
            if (!Schema::hasColumn('opening_stocks', 'alert_quantity')) {
                $table->decimal('alert_quantity', 14, 4)->nullable()->after('sale_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opening_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('opening_stocks', 'alert_quantity')) {
                $table->dropColumn('alert_quantity');
            }
            if (Schema::hasColumn('opening_stocks', 'sale_price')) {
                $table->dropColumn('sale_price');
            }
        });
    }
};
