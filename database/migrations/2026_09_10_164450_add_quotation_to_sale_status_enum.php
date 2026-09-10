<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN sale_status ENUM('booked','posted','cancelled','returned','quotation') DEFAULT 'booked'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sales MODIFY COLUMN sale_status ENUM('booked','posted','cancelled','returned') DEFAULT 'booked'");
    }
};
