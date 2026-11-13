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
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'comment')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->text('comment')->nullable()->after('attachment');
            });
        }

        if (Schema::hasTable('delivery_challans') && !Schema::hasColumn('delivery_challans', 'comment')) {
            Schema::table('delivery_challans', function (Blueprint $table) {
                $table->text('comment')->nullable()->after('remarks');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales') && Schema::hasColumn('sales', 'comment')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('comment');
            });
        }

        if (Schema::hasTable('delivery_challans') && Schema::hasColumn('delivery_challans', 'comment')) {
            Schema::table('delivery_challans', function (Blueprint $table) {
                $table->dropColumn('comment');
            });
        }
    }
};
