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
            if (!Schema::hasColumn('purchases', 'advance_adjusted')) {
                $table->decimal('advance_adjusted', 15, 2)->default(0)->after('paid_amount');
            }
            if (!Schema::hasColumn('purchases', 'advance_settlement_note')) {
                $table->text('advance_settlement_note')->nullable()->after('advance_adjusted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'advance_adjusted')) {
                $table->dropColumn('advance_adjusted');
            }
            if (Schema::hasColumn('purchases', 'advance_settlement_note')) {
                $table->dropColumn('advance_settlement_note');
            }
        });
    }
};
