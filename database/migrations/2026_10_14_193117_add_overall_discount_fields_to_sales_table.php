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
            $table->string('overall_discount_type', 20)->default('pkr')->nullable()->after('total_bill_amount');
            $table->decimal('overall_discount_value', 15, 2)->default(0)->nullable()->after('overall_discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['overall_discount_type', 'overall_discount_value']);
        });
    }
};
