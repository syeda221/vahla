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
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE sales MODIFY COLUMN sale_status VARCHAR(50) NOT NULL DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE sales MODIFY COLUMN sale_status ENUM('booked', 'posted', 'cancelled', 'returned') NOT NULL DEFAULT 'booked'");
        });
    }
};
