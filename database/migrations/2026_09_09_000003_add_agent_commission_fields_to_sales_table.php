<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('agent_id')->nullable()->after('customer_id');
            $table->string('commission_type')->nullable()->after('agent_id');
            $table->decimal('commission_value', 15, 2)->nullable()->after('commission_type');
            $table->decimal('commission_amount', 15, 2)->default(0)->after('commission_value');
            $table->decimal('commission_paid', 15, 2)->default(0)->after('commission_amount');
            $table->unsignedBigInteger('commission_expense_voucher_id')->nullable()->after('commission_paid');

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn([
                'agent_id', 'commission_type', 'commission_value',
                'commission_amount', 'commission_paid', 'commission_expense_voucher_id',
            ]);
        });
    }
};