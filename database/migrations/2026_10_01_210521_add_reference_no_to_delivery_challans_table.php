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
            $table->string('reference_no')->nullable()->after('dc_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->dropColumn('reference_no');
        });
    }
};
