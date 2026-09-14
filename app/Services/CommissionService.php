<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Agent;
use App\Models\AgentLedger;
use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\JournalEntry;
use App\Models\Sale;
use App\Models\VoucherDetail;
use App\Models\VoucherMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionService
{
    protected $balanceService;
    protected $journalService;

    public function __construct()
    {
        $this->balanceService = app(BalanceService::class);
        $this->journalService = app(JournalEntryService::class);
    }

    /**
     * Determine the total paid amount for a given sale by summing
     * the settlement (AR credit) side of all receipt vouchers linked to that invoice.
     */
    public function getSalePaidAmount(Sale $sale): float
    {
        $arAccountId = $this->balanceService->getAccountsReceivableId();

        $total = VoucherMaster::where('voucher_type', VoucherMaster::TYPE_RECEIPT)
            ->where('status', VoucherMaster::STATUS_POSTED)
            ->where('remarks', 'like', "%#{$sale->invoice_no}%")
            ->with('details')
            ->get()
            ->sum(function ($voucher) use ($arAccountId) {
                return (float) $voucher->details
                    ->where('account_id', $arAccountId)
                    ->sum('credit');
            });

        // Fallback: if no voucher based settlement found, use total AR
        // credit journal entries recorded against this invoice description.
        if ($total <= 0 && $sale->customer_id) {
            $total = (float) JournalEntry::where('source_id', $sale->id)
                ->where('source_type', Sale::class)
                ->where('account_id', $arAccountId)
                ->sum('credit');
        }

        return $total;
    }

    /**
     * Check whether a sale has been fully settled/paid.
     */
    public function isSaleFullyPaid(Sale $sale): bool
    {
        if ($sale->sale_status !== 'posted') {
            return false;
        }

        $netPayable = (float) $sale->total_net;

        if ($netPayable <= 0) {
            return true;
        }

        $paid = $this->getSalePaidAmount($sale);

        return $paid >= ($netPayable - 0.05);
    }

    /**
     * Main entry point called when a sale is saved/posted.
     * Creates the commission payable ledger entry for the agent.
     */
    public function processSaleCommission(Sale $sale)
    {
        $amount = (float) $sale->commission_amount;

        if (! $sale->agent_id || $amount <= 0) {
            return;
        }

        // Only create the ledger entry once per sale.
        $alreadyExists = AgentLedger::where('sale_invoice_no', $sale->invoice_no)
            ->where('commission_amount', '>', 0)
            ->exists();

        if (! $alreadyExists) {
            $this->createCommissionPayableEntry($sale);
        }

        // If the sale is paid in full at save time, immediately recognise the expense.
        if ($this->isSaleFullyPaid($sale) && ! $sale->commission_expense_voucher_id) {
            $this->createCommissionExpenseVoucher($sale);
        }
    }

    /**
     * Create a commission payable ledger entry in the agent's ledger.
     * Used when a sale with commission is saved.
     */
    public function createCommissionPayableEntry(Sale $sale)
    {
        $agent = Agent::find($sale->agent_id);
        if (! $agent) {
            return;
        }

        $amount = (float) $sale->commission_amount;
        if ($amount <= 0) {
            return;
        }

        $lastBalance = $agent->last_balance;

        AgentLedger::create([
            'agent_id' => $agent->id,
            'date' => $sale->created_at ? $sale->created_at->format('Y-m-d') : now()->format('Y-m-d'),
            'reference' => 'Sale Invoice #' . $sale->invoice_no,
            'sale_invoice_no' => $sale->invoice_no,
            'commission_amount' => $amount,
            'payment_amount' => 0,
            'balance' => $lastBalance + $amount,
            'remarks' => 'Commission Payable against Sale Invoice #' . $sale->invoice_no,
        ]);

        Log::info("Commission Payable created for Agent #{$agent->id} - Sale {$sale->invoice_no}: Rs {$amount}");
    }

    /**
     * When a sale becomes fully paid, create the Commission Expense Voucher.
     * Debit: Commission Expense | Credit: Commission Payable
     * Guarded against duplicates.
     */
    public function createCommissionExpenseVoucher(Sale $sale)
    {
        if (! $sale->agent_id || (float) $sale->commission_amount <= 0) {
            return null;
        }

        // Duplicate guard: if we already created an expense voucher for this sale, skip.
        if ($sale->commission_expense_voucher_id) {
            return ExpenseVoucher::find($sale->commission_expense_voucher_id);
        }

        $exists = ExpenseVoucher::where('reference_no', (string) $sale->invoice_no)
            ->where('type', 'agent')
            ->where('party_id', (string) $sale->agent_id)
            ->where('total_amount', (float) $sale->commission_amount)
            ->first();

        if ($exists) {
            $sale->commission_expense_voucher_id = $exists->id;
            $sale->save();

            return $exists;
        }

        $amount = (float) $sale->commission_amount;
        $agent = Agent::find($sale->agent_id);
        $date = now()->format('Y-m-d');

        DB::beginTransaction();

        try {
            // Ensure the "Agent Commission" expense category exists.
            $category = ExpenseCategory::firstOrCreate(
                ['name' => 'Agent Commission'],
                []
            );

            // Ensure the Agent Commission Expense account exists.
            $expenseAccountId = $this->balanceService->getCommissionExpenseId();
            $payableAccountId = $this->balanceService->getCommissionPayableId();

            // Create Expense Voucher record
            $voucher = ExpenseVoucher::create([
                'evid' => ExpenseVoucher::generateInvoiceNo(),
                'entry_date' => $date,
                'type' => 'agent',
                'party_id' => (string) $agent->id,
                'tel' => $agent->contact_number,
                'remarks' => 'Commission against Sale ' . $sale->invoice_no,
                'reference_no' => (string) $sale->invoice_no,
                'narration_id' => json_encode([$category->id]),
                'row_account_head' => json_encode(['Agent Commission']),
                'row_account_id' => json_encode([$expenseAccountId]),
                'amount' => json_encode([$amount]),
                'total_amount' => $amount,
            ]);

            // Journal entries: Expiry expense recognised on settlement.
            $description = 'Commission against Sale ' . $sale->invoice_no . ' for Agent ' . $agent->name;

            // Debit Agent Commission Expense
            $this->journalService->recordEntry(
                $voucher,
                $expenseAccountId,
                $amount,
                0,
                $description,
                $date
            );

            // Credit Commission Payable (liability to the agent)
            $this->journalService->recordEntry(
                $voucher,
                $payableAccountId,
                0,
                $amount,
                $description,
                $date,
                $agent
            );

            // Link voucher back to the sale
            $sale->commission_expense_voucher_id = $voucher->id;
            $sale->save();

            DB::commit();

            Log::info("Commission Expense Voucher {$voucher->evid} created for Sale {$sale->invoice_no}: Rs {$amount}");

            return $voucher;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Commission Expense Voucher Failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Called after a payment/receipt is recorded for a sale.
     * If the sale becomes fully paid, trigger the expense voucher.
     */
    public function checkAndProcessCommissionOnPayment(Sale $sale)
    {
        if ($sale->commission_expense_voucher_id) {
            return ExpenseVoucher::find($sale->commission_expense_voucher_id);
        }

        if (! $sale->agent_id || (float) $sale->commission_amount <= 0) {
            return null;
        }

        if ($this->isSaleFullyPaid($sale)) {
            return $this->createCommissionExpenseVoucher($sale);
        }

        return null;
    }

    /**
     * Record a commission payment to an agent.
     * Reduces the outstanding commission balance in the agent ledger.
     */
    public function recordCommissionPayment(Agent $agent, float $amount, string $reference = null, string $remarks = null, int $paymentAccountId = null)
    {
        if ($amount <= 0) {
            return null;
        }

        $lastBalance = $agent->last_balance;
        $newBalance = max(0, $lastBalance - $amount);
        $date = now()->format('Y-m-d');

        DB::beginTransaction();

        try {
            $entry = AgentLedger::create([
                'agent_id' => $agent->id,
                'date' => $date,
                'reference' => $reference ?: 'Commission Payment',
                'sale_invoice_no' => null,
                'commission_amount' => 0,
                'payment_amount' => $amount,
                'balance' => $newBalance,
                'remarks' => $remarks ?: 'Commission Paid to ' . $agent->name,
            ]);

            // Journal entries: settle the payable.
            $payableAccountId = $this->balanceService->getCommissionPayableId();
            $cashAccountId = $paymentAccountId ?: $this->balanceService->getCashAccountId();
            $description = 'Commission Payment to Agent ' . $agent->name;

            // Debit Commission Payable
            $this->journalService->recordEntry(
                $entry,
                $payableAccountId,
                $amount,
                0,
                $description,
                $date,
                $agent
            );

            // Credit Cash/Bank
            $this->journalService->recordEntry(
                $entry,
                $cashAccountId,
                0,
                $amount,
                $description,
                $date
            );

            DB::commit();

            Log::info("Commission Payment of Rs {$amount} recorded for Agent #{$agent->id}. New balance: {$newBalance}");

            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Commission Payment Failed: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Reverse all commission related entries for a sale (used when editing/deleting).
     */
    public function reverseCommissionEntries(Sale $sale)
    {
        // Delete agent ledger entries tied to this sale's invoice.
        AgentLedger::where('sale_invoice_no', $sale->invoice_no)->delete();

        // If an expense voucher was created, reverse its journal entries and delete it.
        if ($sale->commission_expense_voucher_id) {
            $voucher = ExpenseVoucher::find($sale->commission_expense_voucher_id);
            if ($voucher) {
                try {
                    $this->journalService->reverseEntriesForSource($voucher);
                    $voucher->delete();
                } catch (\Exception $e) {
                    Log::error('Reverse Commission Expense Voucher Failed: ' . $e->getMessage());
                }
            }

            $sale->commission_expense_voucher_id = null;
            $sale->save();
        }
    }
}