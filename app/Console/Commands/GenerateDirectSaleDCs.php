<?php

namespace App\Console\Commands;

use App\Http\Controllers\SaleController;
use App\Models\Sale;
use Illuminate\Console\Command;

class GenerateDirectSaleDCs extends Command
{
    protected $signature = 'sales:generate-direct-dcs {--force : Re-sync even if DC already exists}';

    protected $description = 'Generate missing Delivery Challans (DCs) for all posted Direct Sales';

    public function handle()
    {
        $this->info('Starting Delivery Challan generation for Direct Sales...');

        $query = Sale::with(['items', 'deliveryChallans'])
            ->whereIn('sale_status', ['posted', 'returned'])
            ->where(function ($q) {
                $q->where('sale_type', 'direct_sale')
                  ->orWhereNull('sale_type');
            });

        if (!$this->option('force')) {
            $query->whereDoesntHave('deliveryChallans');
        }

        $sales = $query->get();

        if ($sales->isEmpty()) {
            $this->info('All direct sales already have Delivery Challans generated.');
            return 0;
        }

        $this->info("Found {$sales->count()} direct sales to process.");

        $controller = app(SaleController::class);
        $count = 0;

        foreach ($sales as $sale) {
            try {
                $dc = $controller->syncDeliveryChallanForDirectSale($sale);
                if ($dc) {
                    $count++;
                    $this->line("✓ Generated DC #{$dc->dc_number} for Sale #{$sale->invoice_no}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Failed for Sale #{$sale->invoice_no}: " . $e->getMessage());
            }
        }

        $this->info("Successfully generated/synced {$count} Delivery Challans.");
        return 0;
    }
}
