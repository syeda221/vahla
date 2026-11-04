<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Services\BalanceService;

class SyncCustomerBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:sync-balances 
                            {--preview : Preview differences without making any changes to the database}
                            {--mismatches-only : Only show customers that have a balance mismatch}
                            {--sync : Apply the balance synchronization}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preview or synchronize all customer balances with the official double-entry ledger';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService)
    {
        $isPreview = $this->option('preview') || !$this->option('sync');
        $mismatchesOnly = $this->option('mismatches-only');

        $this->info($isPreview ? "=== CUSTOMER BALANCES PREVIEW (READ ONLY) ===" : "=== SYNCHRONIZING CUSTOMER BALANCES ===");

        $customers = Customer::orderBy('id', 'asc')->get();
        $tableRows = [];
        $discrepancyCount = 0;
        $updatedCount = 0;

        foreach ($customers as $customer) {
            $trueBalance = $balanceService->getCustomerBalance($customer->id);
            $currentMaster = (float) $customer->previous_balance;
            $diff = round($trueBalance - $currentMaster, 2);
            $hasMismatch = abs($diff) > 0.01;

            if ($hasMismatch) {
                $discrepancyCount++;
            }

            if ($mismatchesOnly && !$hasMismatch) {
                continue;
            }

            $statusText = $hasMismatch ? '⚠️ MISMATCH' : '✅ OK';

            $tableRows[] = [
                'id' => $customer->id,
                'name' => mb_strimwidth($customer->customer_name ?? 'N/A', 0, 25, '...'),
                'master_bal' => number_format($currentMaster, 2),
                'ledger_bal' => number_format($trueBalance, 2),
                'diff' => number_format($diff, 2),
                'status' => $statusText,
            ];

            // If not in preview mode, apply the sync
            if (!$isPreview && $hasMismatch) {
                $customer->previous_balance = $trueBalance;
                $customer->save();

                $latestLedger = CustomerLedger::where('customer_id', $customer->id)
                    ->latest('id')
                    ->first();

                if ($latestLedger && abs((float)$latestLedger->closing_balance - $trueBalance) > 0.01) {
                    $latestLedger->closing_balance = $trueBalance;
                    $latestLedger->save();
                }

                $updatedCount++;
            }
        }

        $this->table(
            ['ID', 'Customer Name', 'Current Balance', 'True Ledger Due', 'Difference', 'Status'],
            $tableRows
        );

        $this->newLine();
        $this->info("Total Customers Scanned: " . $customers->count());
        $this->info("Customers with Mismatches: " . $discrepancyCount);

        if ($isPreview) {
            $this->warn("[PREVIEW MODE] No changes were made to the database.");
            if ($discrepancyCount > 0) {
                $this->comment("To fix and sync all discrepancies, run: php artisan customers:sync-balances --sync");
            } else {
                $this->info("Everything is already in perfect sync!");
            }
        } else {
            $this->info("Sync Completed! Total customers successfully updated: {$updatedCount}");
        }

        return 0;
    }
}