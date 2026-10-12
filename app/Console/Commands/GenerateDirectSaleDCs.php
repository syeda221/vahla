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
        $this->info('Direct sales no longer generate Delivery Challans. Stock is deducted directly on the Sale Invoice, and the Customer Ledger is maintained directly without a DC.');
        return 0;
    }
}
