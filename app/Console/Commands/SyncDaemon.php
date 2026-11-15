<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SyncController;
use Illuminate\Http\Request;

class SyncDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:daemon {--interval=10 : Interval in seconds between sync pulses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Autonomous real-time 2-way sync daemon between Local and Cloud';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        if ($interval < 3) $interval = 3;

        $this->info("[" . date('Y-m-d H:i:s') . "] Vahla 2-Way Real-time Auto-Sync Daemon started (Interval: {$interval}s)...");

        $syncController = app(SyncController::class);

        while (true) {
            try {
                $response = $syncController->syncToCloud(new Request());
                $data = json_decode($response->getContent(), true);

                if ($data && isset($data['status']) && $data['status'] === 'success') {
                    $pulledCust = $data['pulled_customers'] ?? 0;
                    $pulledSales = $data['pulled_sales'] ?? 0;
                    $pushedSales = $data['synced_sales'] ?? 0;
                    $pushedCust = $data['synced_customers'] ?? 0;

                    if ($pulledCust > 0 || $pulledSales > 0 || $pushedSales > 0 || $pushedCust > 0) {
                        $this->info("[" . date('H:i:s') . "] Auto-Synced: +{$pulledCust} customer(s), +{$pulledSales} sale(s) from Cloud | Pushed: {$pushedSales} sale(s), {$pushedCust} customer(s).");
                    }
                }
            } catch (\Throwable $e) {
                // If offline / internet unavailable, silently catch and retry next cycle
            }

            sleep($interval);
        }
    }
}
