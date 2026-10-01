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
        $tables = [
            'receipts_vouchers',
            'payment_vouchers',
            'voucher_masters',
            'customer_payments',
            'vendor_payments'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'attachment')) {
                        $table->string('attachment')->nullable();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'receipts_vouchers',
            'payment_vouchers',
            'voucher_masters',
            'customer_payments',
            'vendor_payments'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'attachment')) {
                        $table->dropColumn('attachment');
                    }
                });
            }
        }
    }
};
