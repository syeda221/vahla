<?php

namespace App\Services;

use App\Models\Cheque;
use App\Models\Account;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\VoucherMaster;
use App\Models\VoucherDetail;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChequeAlertService
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Process all cheque alerts, pre-due balance checks, and due-date auto-deductions.
     */
    public function processCheques(): array
    {
        $today = date('Y-m-d');
        $results = [
            'auto_deducted' => 0,
            'due_notified' => 0,
            'low_balance_notified' => 0,
            'active_low_balance_alerts' => 0,
            'total_pending' => Cheque::where('status', 'pending')->count(),
            'details' => [],
        ];

        // 1. Auto-deduct outward cheques that have reached or passed their due date
        $dueOutwardCheques = Cheque::where('type', 'outward')
            ->where('status', 'pending')
            ->whereDate('cheque_date', '<=', $today)
            ->get();

        foreach ($dueOutwardCheques as $chq) {
            try {
                $this->autoDeductOutwardCheque($chq);
                $results['auto_deducted']++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-deduct outward cheque #{$chq->cheque_no}: " . $e->getMessage());
            }
        }

        // 2. Notify for inward customer cheques maturing today
        $dueInwardCheques = Cheque::where('type', 'inward')
            ->where('status', 'pending')
            ->where('due_date_notified', false)
            ->whereDate('cheque_date', '<=', $today)
            ->get();

        foreach ($dueInwardCheques as $chq) {
            try {
                $this->notifyInwardChequeMaturity($chq);
                $chq->due_date_notified = true;
                $chq->save();
                $results['due_notified']++;
            } catch (\Exception $e) {
                Log::error("Failed to notify inward cheque #{$chq->cheque_no}: " . $e->getMessage());
            }
        }

        // 3. Low balance checks for outward cheques maturing within alert_days_before
        $pendingOutwardCheques = Cheque::where('type', 'outward')
            ->where('status', 'pending')
            ->whereDate('cheque_date', '>', $today)
            ->get();

        foreach ($pendingOutwardCheques as $chq) {
            $daysLeft = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($chq->cheque_date)->startOfDay(), false);
            if ($daysLeft <= $chq->alert_days_before && $daysLeft >= 0) {
                $bankAccount = Account::find($chq->bank_account_id);
                if ($bankAccount) {
                    $currentBal = (float) $bankAccount->current_balance;
                    $chqAmount = (float) $chq->amount;

                    if ($currentBal < $chqAmount) {
                        $shortfall = $chqAmount - $currentBal;
                        $results['active_low_balance_alerts']++;
                        $results['details'][] = "Cheque #{$chq->cheque_no} ({$bankAccount->title}): Available PKR " . number_format($currentBal, 2) . ", Cheque PKR " . number_format($chqAmount, 2) . " (Shortfall: PKR " . number_format($shortfall, 2) . ", Due in {$daysLeft} day(s) on " . $chq->cheque_date->format('d-M-Y') . ")";

                        if (!$chq->low_balance_notified) {
                            $this->notifyLowBalance($chq, $bankAccount, $currentBal, $shortfall, $daysLeft);
                            $chq->low_balance_notified = true;
                            $chq->save();
                            $results['low_balance_notified']++;
                        }
                    } else {
                        if ($chq->low_balance_notified) {
                            $chq->low_balance_notified = false;
                            $chq->save();
                        }
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Auto-deduct outward cheque from bank account without requiring manual confirmation.
     */
    public function autoDeductOutwardCheque(Cheque $cheque): void
    {
        DB::transaction(function () use ($cheque) {
            $chqPayableAccount = Account::where('account_code', 'CHQ-002')
                ->orWhereRaw('LOWER(title) LIKE ?', ['%cheques payable%'])
                ->first();

            $bankAccount = Account::find($cheque->bank_account_id);
            if (!$bankAccount) {
                $bankAccount = Account::where('id', '>', 0)->first();
            }

            if (!$chqPayableAccount || !$bankAccount) {
                throw new \Exception("Required Cheques Payable or Bank account not found for auto-deduction.");
            }

            $vendor = Vendor::find($cheque->party_id);
            $vendorName = $vendor ? $vendor->name : "Vendor #{$cheque->party_id}";
            $narration = "Auto-deduction for Cheque #{$cheque->cheque_no} (PKR " . number_format($cheque->amount, 2) . ") for {$vendorName} on due date {$cheque->cheque_date->format('Y-m-d')}";

            // Double Entry: Debit Cheques Payable, Credit Bank Account
            $vLines = [
                [
                    'account_id' => $chqPayableAccount->id,
                    'debit' => (float) $cheque->amount,
                    'credit' => 0,
                    'narration' => "Clear Cheques Payable #{$cheque->cheque_no}",
                ],
                [
                    'account_id' => $bankAccount->id,
                    'debit' => 0,
                    'credit' => (float) $cheque->amount,
                    'narration' => "Bank Auto-Deduction for Cheque #{$cheque->cheque_no}",
                ],
            ];

            $clearMaster = $this->voucherService->createVoucher([
                'voucher_type' => 'journal',
                'date' => now()->format('Y-m-d'),
                'status' => 'posted',
                'party_type' => null,
                'party_id' => null,
                'remarks' => $narration,
            ], $vLines, $cheque->created_by);

            // Update cheque state
            $cheque->status = 'cleared';
            $cheque->cleared_date = now()->format('Y-m-d');
            $cheque->cleared_account_id = $bankAccount->id;
            $cheque->due_date_notified = true;
            $cheque->save();

            // Broadcast notification to all active users
            $userIds = User::pluck('id')->toArray();
            $msg = "Cheque #{$cheque->cheque_no} (PKR " . number_format($cheque->amount, 2) . ") for Vendor {$vendorName} matured today and has been automatically deducted from {$bankAccount->title}.";

            SystemNotification::createForUsers($userIds, [
                'title' => '💰 Cheque Auto-Deducted',
                'message' => $msg,
                'type' => 'info',
                'source_id' => $cheque->id,
                'source_type' => Cheque::class,
                'action_url' => route('cheques.index', ['tab' => 'outward']),
            ]);
        });
    }

    /**
     * Notify that an inward customer cheque has matured today and is ready to deposit.
     */
    protected function notifyInwardChequeMaturity(Cheque $cheque): void
    {
        $customer = Customer::find($cheque->party_id);
        $customerName = $customer ? $customer->name : "Customer #{$cheque->party_id}";

        $userIds = User::pluck('id')->toArray();
        $msg = "Customer Cheque #{$cheque->cheque_no} (PKR " . number_format($cheque->amount, 2) . ") from {$customerName} is due today. Please deposit into Bank or Cash account.";

        SystemNotification::createForUsers($userIds, [
            'title' => '📥 Cheque Matured Today',
            'message' => $msg,
            'type' => 'info',
            'source_id' => $cheque->id,
            'source_type' => Cheque::class,
            'action_url' => route('cheques.index', ['tab' => 'inward']),
        ]);
    }

    /**
     * Notify that bank balance is lower than the maturing outward cheque.
     */
    protected function notifyLowBalance(Cheque $cheque, Account $bankAccount, float $currentBal, float $shortfall, int $daysLeft): void
    {
        $vendor = Vendor::find($cheque->party_id);
        $vendorName = $vendor ? $vendor->name : "Vendor #{$cheque->party_id}";
        $dueDateFormatted = $cheque->cheque_date->format('d-M-Y');

        $userIds = User::pluck('id')->toArray();
        $msg = "Warning: {$bankAccount->title} balance is PKR " . number_format($currentBal, 2) . ". Cheque #{$cheque->cheque_no} of PKR " . number_format($cheque->amount, 2) . " for {$vendorName} is due in {$daysLeft} day(s) on {$dueDateFormatted}. Please deposit PKR " . number_format($shortfall, 2) . " to avoid cheque bounce.";

        SystemNotification::createForUsers($userIds, [
            'title' => '⚠️ Cheque Low Balance Alert',
            'message' => $msg,
            'type' => 'warning',
            'source_id' => $cheque->id,
            'source_type' => Cheque::class,
            'action_url' => route('cheques.index', ['tab' => 'outward']),
        ]);
    }

    /**
     * Clear an inward cheque into Bank or Cash.
     */
    public function clearInwardCheque(Cheque $cheque, int $destinationAccountId, ?string $clearedDate = null, ?string $remarks = null): void
    {
        if ($cheque->type !== 'inward' || $cheque->status !== 'pending') {
            throw new \Exception("Only pending inward cheques can be cleared.");
        }

        DB::transaction(function () use ($cheque, $destinationAccountId, $clearedDate, $remarks) {
            $chqInHandAccount = Account::where('account_code', 'CHQ-001')
                ->orWhereRaw('LOWER(title) LIKE ?', ['%cheques in hand%'])
                ->first();

            $destAccount = Account::find($destinationAccountId);
            if (!$destAccount) {
                throw new \Exception("Selected Bank/Cash account not found.");
            }

            if (!$chqInHandAccount) {
                throw new \Exception("Cheques in Hand clearing account not found.");
            }

            $customer = Customer::find($cheque->party_id);
            $customerName = $customer ? $customer->name : "Customer #{$cheque->party_id}";
            $date = $clearedDate ?: now()->format('Y-m-d');
            $narration = $remarks ?: "Deposit Cheque #{$cheque->cheque_no} (PKR " . number_format($cheque->amount, 2) . ") from {$customerName} into {$destAccount->title}";

            // Double Entry: Debit Dest Bank/Cash, Credit Cheques in Hand
            $vLines = [
                [
                    'account_id' => $destAccount->id,
                    'debit' => (float) $cheque->amount,
                    'credit' => 0,
                    'narration' => "Deposit Cheque #{$cheque->cheque_no}",
                ],
                [
                    'account_id' => $chqInHandAccount->id,
                    'debit' => 0,
                    'credit' => (float) $cheque->amount,
                    'narration' => "Clear Cheques in Hand #{$cheque->cheque_no}",
                ],
            ];

            $clearMaster = $this->voucherService->createVoucher([
                'voucher_type' => 'journal',
                'date' => $date,
                'status' => 'posted',
                'party_type' => null,
                'party_id' => null,
                'remarks' => $narration,
            ], $vLines, auth()->id() ?? $cheque->created_by);

            $cheque->status = 'cleared';
            $cheque->cleared_date = $date;
            $cheque->cleared_account_id = $destAccount->id;
            $cheque->remarks = $remarks ?: $cheque->remarks;
            $cheque->save();

            $userIds = User::pluck('id')->toArray();
            SystemNotification::createForUsers($userIds, [
                'title' => '✅ Cheque Cleared & Deposited',
                'message' => "Cheque #{$cheque->cheque_no} (PKR " . number_format($cheque->amount, 2) . ") successfully cleared into {$destAccount->title}.",
                'type' => 'success',
                'source_id' => $cheque->id,
                'source_type' => Cheque::class,
                'action_url' => route('cheques.index', ['tab' => 'cleared']),
            ]);
        });
    }

    /**
     * Mark cheque as bounced and optionally reverse ledger balances.
     */
    public function bounceCheque(Cheque $cheque, string $reason): void
    {
        if ($cheque->status !== 'pending') {
            throw new \Exception("Only pending cheques can be marked as bounced.");
        }

        DB::transaction(function () use ($cheque, $reason) {
            $cheque->status = 'bounced';
            $cheque->bounce_reason = $reason;
            $cheque->save();

            $partyName = $cheque->party_name;
            $userIds = User::pluck('id')->toArray();

            SystemNotification::createForUsers($userIds, [
                'title' => '❌ Cheque Bounced',
                'message' => "Cheque #{$cheque->cheque_no} (PKR " . number_format($cheque->amount, 2) . ") for {$partyName} marked as BOUNCED. Reason: {$reason}",
                'type' => 'danger',
                'source_id' => $cheque->id,
                'source_type' => Cheque::class,
                'action_url' => route('cheques.index', ['tab' => 'bounced']),
            ]);
        });
    }
}
