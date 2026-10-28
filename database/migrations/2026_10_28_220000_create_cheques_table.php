<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cheques')) {
            Schema::create('cheques', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['inward', 'outward'])->comment('inward = customer receipt, outward = vendor payment');
                $table->string('voucher_type', 50)->comment('receipt_voucher or payment_voucher');
                $table->unsignedBigInteger('voucher_id');
                $table->unsignedBigInteger('voucher_master_id')->nullable();
                $table->enum('party_type', ['customer', 'vendor']);
                $table->unsignedBigInteger('party_id');
                $table->string('cheque_no', 100);
                $table->date('cheque_date')->comment('Maturity / Due Date');
                $table->date('issue_date')->comment('Voucher Creation Date');
                $table->decimal('amount', 15, 2);
                $table->string('bank_name', 150)->nullable();
                $table->unsignedBigInteger('bank_account_id')->nullable()->comment('Issuing bank account for outward; deposit target for inward');
                $table->unsignedBigInteger('cleared_account_id')->nullable()->comment('Bank/Cash account where money was cleared');
                $table->enum('status', ['pending', 'cleared', 'bounced', 'cancelled'])->default('pending');
                $table->date('cleared_date')->nullable();
                $table->integer('alert_days_before')->default(5)->comment('Days prior to due date to check balance');
                $table->boolean('low_balance_notified')->default(false);
                $table->boolean('due_date_notified')->default(false);
                $table->text('bounce_reason')->nullable();
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['status', 'cheque_date']);
                $table->index('bank_account_id');
                $table->index(['party_type', 'party_id']);
            });
        }

        // Ensure "Cheques in Hand" account exists
        $chqInHand = DB::table('accounts')->where('account_code', 'CHQ-001')
            ->orWhereRaw('LOWER(title) LIKE ?', ['%cheques in hand%'])
            ->first();
        if (!$chqInHand) {
            $bankHead = DB::table('account_heads')->whereRaw('LOWER(name) LIKE ?', ['%bank%'])->first();
            $headId = $bankHead ? $bankHead->id : 2;
            DB::table('accounts')->insert([
                'title' => 'Cheques in Hand',
                'account_code' => 'CHQ-001',
                'head_id' => $headId,
                'type' => 'Debit',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure "Cheques Payable" account exists
        $chqPayable = DB::table('accounts')->where('account_code', 'CHQ-002')
            ->orWhereRaw('LOWER(title) LIKE ?', ['%cheques payable%'])
            ->first();
        if (!$chqPayable) {
            $bankHead = DB::table('account_heads')->whereRaw('LOWER(name) LIKE ?', ['%bank%'])->first();
            $headId = $bankHead ? $bankHead->id : 2;
            DB::table('accounts')->insert([
                'title' => 'Cheques Payable',
                'account_code' => 'CHQ-002',
                'head_id' => $headId,
                'type' => 'Credit',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
