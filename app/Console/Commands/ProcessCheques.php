<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ChequeAlertService;

class ProcessCheques extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cheques:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process cheque due-date auto-deductions, maturity alerts, and pre-due low balance warnings';

    /**
     * Execute the console command.
     */
    public function handle(ChequeAlertService $service)
    {
        $this->info("Processing cheques...");
        $res = $service->processCheques();
        $this->info("Auto-deducted: {$res['auto_deducted']}");
        $this->info("Due alerts: {$res['due_notified']}");
        $this->info("Newly notified: {$res['low_balance_notified']}");
        $this->info("Active low balance warnings: {$res['active_low_balance_alerts']}");
        if (!empty($res['details'])) {
            foreach ($res['details'] as $d) {
                $this->warn("  * {$d}");
            }
        }
        $this->info("Cheques processed successfully.");
        return 0;
    }
}
