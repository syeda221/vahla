<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Services\BalanceService;

class SyncCustomerBalances extends Command
{
    protected $signature = 'customers:sync-balances';
    protected $description = 'Synchronize all customer balances with the official journal ledger';

    public function handle(BalanceService $balanceService)
    {
        $this->info("Starting Customer Balance Synchronization...");

        $customers = Customer::all();
        $totalUpdated = 0;

        foreach ($customers as $customer) {
            $trueBalance = $balanceService->getCustomerBalance($customer->id);

            if (abs((float)$customer->previous_balance - $trueBalance) > 0.001) {
                $customer->previous_balance = $trueBalance;
                $customer->save();
                $totalUpdated++;
            }

            $latestLedger = CustomerLedger::where('customer_id', $customer->id)
                ->latest('id')
                ->first();

            if ($latestLedger && abs((float)$latestLedger->closing_balance - $trueBalance) > 0.001) {
                $latestLedger->closing_balance = $trueBalance;
                $latestLedger->save();
            }
        }

        $this->info("Sync completed! Total customers updated: {$totalUpdated} / {$customers->count()}");
        return 0;
    }
}