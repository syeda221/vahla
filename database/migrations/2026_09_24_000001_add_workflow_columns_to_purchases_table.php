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
            if (!Schema::hasColumn('purchases', 'purchase_type')) {
                $table->string('purchase_type')->default('direct_purchase')->after('id');
            }
            if (!Schema::hasColumn('purchases', 'purchase_status')) {
                $table->string('purchase_status')->default('posted')->after('purchase_type');
            }
            if (!Schema::hasColumn('purchases', 'receiving_status')) {
                $table->string('receiving_status')->default('received')->after('purchase_status');
            }
            if (!Schema::hasColumn('purchases', 'parent_po_id')) {
                $table->unsignedBigInteger('parent_po_id')->nullable()->after('receiving_status');
            }
            if (!Schema::hasColumn('purchases', 'credit_days')) {
                $table->integer('credit_days')->default(0)->after('purchase_date');
            }
            if (!Schema::hasColumn('purchases', 'vendor_bill_no')) {
                $table->string('vendor_bill_no')->nullable()->after('invoice_no');
            }
            if (!Schema::hasColumn('purchases', 'purchase_prefix')) {
                $table->string('purchase_prefix')->default('PINV')->after('vendor_bill_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_type',
                'purchase_status',
                'receiving_status',
                'parent_po_id',
                'credit_days',
                'vendor_bill_no',
                'purchase_prefix'
            ]);
        });
    }
};
