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
        if (Schema::hasTable('receipts_vouchers')) {
            Schema::table('receipts_vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('receipts_vouchers', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('total_amount');
                }
                if (!Schema::hasColumn('receipts_vouchers', 'discount_amount')) {
                    $table->decimal('discount_amount', 15, 2)->default(0)->after('tax_amount');
                }
                if (!Schema::hasColumn('receipts_vouchers', 'received_amount')) {
                    $table->decimal('received_amount', 15, 2)->default(0)->after('discount_amount');
                }
            });
        }

        if (Schema::hasTable('payment_vouchers')) {
            Schema::table('payment_vouchers', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_vouchers', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('total_amount');
                }
                if (!Schema::hasColumn('payment_vouchers', 'discount_amount')) {
                    $table->decimal('discount_amount', 15, 2)->default(0)->after('tax_amount');
                }
                if (!Schema::hasColumn('payment_vouchers', 'paid_amount')) {
                    $table->decimal('paid_amount', 15, 2)->default(0)->after('discount_amount');
                }
            });
        }

        if (Schema::hasTable('voucher_masters')) {
            Schema::table('voucher_masters', function (Blueprint $table) {
                if (!Schema::hasColumn('voucher_masters', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->default(0)->after('total_amount');
                }
                if (!Schema::hasColumn('voucher_masters', 'discount_amount')) {
                    $table->decimal('discount_amount', 15, 2)->default(0)->after('tax_amount');
                }
                if (!Schema::hasColumn('voucher_masters', 'net_amount')) {
                    $table->decimal('net_amount', 15, 2)->default(0)->after('discount_amount');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('receipts_vouchers')) {
            Schema::table('receipts_vouchers', function (Blueprint $table) {
                $columns = ['tax_amount', 'discount_amount', 'received_amount'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('receipts_vouchers', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('payment_vouchers')) {
            Schema::table('payment_vouchers', function (Blueprint $table) {
                $columns = ['tax_amount', 'discount_amount', 'paid_amount'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('payment_vouchers', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('voucher_masters')) {
            Schema::table('voucher_masters', function (Blueprint $table) {
                $columns = ['tax_amount', 'discount_amount', 'net_amount'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('voucher_masters', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
