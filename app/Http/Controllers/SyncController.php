<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncController extends Controller
{
    /**
     * Get current sync status & count of unsynced records
     */
    public function syncStatus(Request $request)
    {
        $unsyncedSales = Sale::where('is_synced', 0)->count();
        $unsyncedCustomers = Customer::where('is_synced', 0)->count();
        $total = $unsyncedSales + $unsyncedCustomers;

        return response()->json([
            'status' => 'success',
            'unsynced_sales' => $unsyncedSales,
            'unsynced_customers' => $unsyncedCustomers,
            'total_unsynced' => $total,
            'cloud_url' => env('CLOUD_SYNC_URL', 'https://test.binsultansweet.com'),
            'local_url' => env('LOCAL_SYNC_URL', 'http://127.0.0.1:8000'),
        ]);
    }

    /**
     * Runs on the local server instance to push unsynced data to cloud
     * and pull newly created cloud data (2-Way Full Sync).
     */
    public function syncToCloud(Request $request)
    {
        $cloudSyncUrl = rtrim(env('CLOUD_SYNC_URL', 'https://test.binsultansweet.com'), '/');
        $syncToken = env('SYNC_TOKEN', 'trendhub_sync_default_token_2026');

        // 1. Fetch unsynced local customers
        $customers = Customer::where('is_synced', 0)->get()->map(function($c) {
            return $c->toArray();
        })->toArray();

        // 2. Fetch unsynced local sales with items
        $sales = Sale::where('is_synced', 0)->get()->map(function($s) {
            $sData = $s->toArray();
            
            // Map customer local ID to UUID
            $cust = Customer::find($s->customer_id);
            $sData['customer_uuid'] = $cust ? $cust->uuid : null;

            // Load items and map product local ID to SKU
            $sData['items'] = SaleItem::where('sale_id', $s->id)->get()->map(function($item) {
                $iData = $item->toArray();
                $prod = Product::find($item->product_id);
                $iData['product_code'] = $prod ? $prod->item_code : null;
                return $iData;
            })->toArray();

            return $sData;
        })->toArray();

        // 3. Collect all known customer & sale UUIDs from local to ask Cloud for missing records
        $knownCustomerUuids = Customer::whereNotNull('uuid')->pluck('uuid')->toArray();
        $knownSaleUuids = Sale::whereNotNull('uuid')->pluck('uuid')->toArray();

        try {
            // Send payload to cloud via HTTP Client (withoutVerifying to bypass SSL issues)
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->withHeaders(['X-Sync-Token' => $syncToken])
                ->post($cloudSyncUrl . '/api/receive-sync', [
                    'customers' => $customers,
                    'sales' => $sales,
                    'known_customer_uuids' => $knownCustomerUuids,
                    'known_sale_uuids' => $knownSaleUuids,
                ]);

            if ($response->successful()) {
                $resData = $response->json();
                if (isset($resData['status']) && $resData['status'] === 'success') {
                    // A. Mark pushed local records as synced
                    if (!empty($customers)) {
                        Customer::whereIn('uuid', collect($customers)->pluck('uuid'))->update(['is_synced' => 1]);
                    }
                    if (!empty($sales)) {
                        Sale::whereIn('uuid', collect($sales)->pluck('uuid'))->update(['is_synced' => 1]);
                    }

                    $pulledCustomersCount = 0;
                    $pulledSalesCount = 0;

                    // B1. Process pulled Cloud customers onto Local
                    $cloudCustomers = $resData['cloud_customers'] ?? [];
                    if (!empty($cloudCustomers)) {
                        $custColumns = \Illuminate\Support\Facades\Schema::getColumnListing('customers');
                        $balanceService = app(\App\Services\BalanceService::class);

                        foreach ($cloudCustomers as $cc) {
                            $uuid = $cc['uuid'] ?? null;
                            if (!$uuid) continue;

                            if (Customer::where('uuid', $uuid)->exists()) {
                                Customer::where('uuid', $uuid)->update(['is_synced' => 1]);
                                continue;
                            }

                            unset($cc['id']);
                            if (isset($cc['customer_id']) && Customer::where('customer_id', $cc['customer_id'])->exists()) {
                                $cc['customer_id'] = 'CUST-' . str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
                            }

                            $filteredCustData = array_intersect_key($cc, array_flip($custColumns));
                            $newCust = new Customer($filteredCustData);
                            $newCust->uuid = $uuid;
                            $newCust->is_synced = 1;
                            $newCust->save();

                            try {
                                $balanceService->ensureCustomerOpeningBalance($newCust);
                            } catch (\Exception $e) {}

                            $pulledCustomersCount++;
                        }
                    }

                    // B2. Process pulled Cloud sales onto Local
                    $cloudSales = $resData['cloud_sales'] ?? [];
                    if (!empty($cloudSales)) {
                        DB::beginTransaction();
                        try {
                            foreach ($cloudSales as $cs) {
                                $uuid = $cs['uuid'] ?? null;
                                if (!$uuid) continue;

                                // If already exists on local, skip
                                if (Sale::where('uuid', $uuid)->exists()) continue;

                                $items = $cs['items'] ?? [];
                                unset($cs['id']);
                                unset($cs['items']);

                                // Match customer on local
                                if (!empty($cs['customer_uuid'])) {
                                    $localCust = Customer::where('uuid', $cs['customer_uuid'])->first();
                                    if ($localCust) {
                                        $cs['customer_id'] = $localCust->id;
                                    }
                                }
                                unset($cs['customer_uuid']);

                                // Filter attributes to only valid columns on Local table
                                $saleColumns = \Illuminate\Support\Facades\Schema::getColumnListing('sales');
                                $filteredSaleData = array_intersect_key($cs, array_flip($saleColumns));

                                $newSale = new Sale($filteredSaleData);
                                $newSale->uuid = $uuid;
                                $newSale->is_synced = 1;
                                $newSale->save();

                                // Update local series counter
                                \App\Models\InvoiceSeries::incrementCounterForInvoice($newSale->invoice_no);

                                foreach ($items as $ci) {
                                    unset($ci['id']);
                                    unset($ci['sale_id']);
                                    if (!empty($ci['product_code'])) {
                                        $localProd = Product::where('item_code', $ci['product_code'])->first();
                                        if ($localProd) {
                                            $ci['product_id'] = $localProd->id;
                                        }
                                    }
                                    unset($ci['product_code']);

                                    $itemColumns = \Illuminate\Support\Facades\Schema::getColumnListing('sale_items');
                                    $filteredItemData = array_intersect_key($ci, array_flip($itemColumns));

                                    $sItem = new SaleItem($filteredItemData);
                                    $sItem->sale_id = $newSale->id;
                                    $sItem->save();
                                }

                                // Post to Local Stock, Customer Ledger & Vouchers
                                if ($newSale->sale_status === 'posted') {
                                    $saleController = app(\App\Http\Controllers\SaleController::class);
                                    $balanceService = app(\App\Services\BalanceService::class);
                                    $transactionService = app(\App\Services\TransactionService::class);

                                    try { $saleController->handleStockImpact($newSale, 'out'); } catch (\Exception $e) {}
                                    try { $saleController->updateLedger($newSale); } catch (\Exception $e) {}
                                    try {
                                        $custForVoucher = $newSale->customer_relation ?? Customer::find($newSale->customer_id);
                                        if ($custForVoucher) {
                                            $date = $newSale->created_at ? $newSale->created_at->format('Y-m-d') : date('Y-m-d');
                                            $balanceService->createSaleVoucher($custForVoucher, $newSale->total_net, $newSale->invoice_no, $date);
                                        }
                                    } catch (\Exception $e) {}
                                    try { $transactionService->createReceiptFromSale($newSale); } catch (\Exception $e) {}
                                }

                                $pulledSalesCount++;
                            }

                            // C. Update product stock on local if provided
                            if (!empty($resData['product_stocks'])) {
                                foreach ($resData['product_stocks'] as $pStock) {
                                    if (isset($pStock['item_code']) && isset($pStock['total_stock_qty'])) {
                                        Product::where('item_code', $pStock['item_code'])->update([
                                            'total_stock_qty' => $pStock['total_stock_qty']
                                        ]);
                                    }
                                }
                            }

                            // D. Update customer balances on local from Cloud
                            if (!empty($resData['customer_balances'])) {
                                foreach ($resData['customer_balances'] as $cBal) {
                                    if (!empty($cBal['uuid'])) {
                                        Customer::where('uuid', $cBal['uuid'])->update([
                                            'previous_balance' => $cBal['previous_balance']
                                        ]);
                                    }
                                }
                            }

                            DB::commit();
                        } catch (\Exception $ex) {
                            DB::rollBack();
                            Log::error('Error applying pulled cloud sales locally: ' . $ex->getMessage());
                        }
                    }

                    $msg = 'Synchronization successful!';
                    if (count($sales) > 0 || count($customers) > 0) {
                        $msg .= ' Pushed: ' . count($sales) . ' sale(s), ' . count($customers) . ' customer(s).';
                    }
                    if ($pulledSalesCount > 0 || $pulledCustomersCount > 0) {
                        $msg .= ' Pulled: ' . $pulledSalesCount . ' sale(s), ' . $pulledCustomersCount . ' customer(s).';
                    }
                    if (count($sales) === 0 && count($customers) === 0 && $pulledSalesCount === 0 && $pulledCustomersCount === 0) {
                        $msg = 'Both systems are already fully synchronized!';
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => $msg,
                        'synced_sales' => count($sales),
                        'pulled_sales' => $pulledSalesCount,
                        'synced_customers' => count($customers),
                        'pulled_customers' => $pulledCustomersCount
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => $resData['message'] ?? 'Sync failed on cloud server.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cloud server error (' . $response->status() . '): ' . $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not reach cloud server. Please check your internet connection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Runs on the cloud server instance to receive pushed data from local
     * AND return new cloud sales that local does not have yet.
     */
    public function receiveSync(Request $request)
    {
        $token = $request->header('X-Sync-Token') ?? $request->input('sync_token');
        if ($token !== env('SYNC_TOKEN', 'trendhub_sync_default_token_2026')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized sync request.'], 401);
        }

        $payload = $request->all();
        $customers = $payload['customers'] ?? [];
        $sales = $payload['sales'] ?? [];
        $knownCustomerUuids = $payload['known_customer_uuids'] ?? [];
        $knownSaleUuids = $payload['known_sale_uuids'] ?? [];

        DB::beginTransaction();
        try {
            // 1. Process incoming customers from Local
            foreach ($customers as $cData) {
                $uuid = $cData['uuid'] ?? (string) Str::uuid();
                unset($cData['id']);
                
                $customer = Customer::where('uuid', $uuid)->first();
                if (!$customer) {
                    if (isset($cData['customer_id']) && Customer::where('customer_id', $cData['customer_id'])->exists()) {
                        $cData['customer_id'] = 'CUST-' . str_pad((Customer::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
                    }
                    $customer = new Customer($cData);
                    $customer->uuid = $uuid;
                    $customer->is_synced = 1;
                    $customer->save();
                } else {
                    $customer->update($cData);
                }
            }

            // 2. Process incoming sales from Local
            $saleController = app(\App\Http\Controllers\SaleController::class);
            $balanceService = app(\App\Services\BalanceService::class);
            $transactionService = app(\App\Services\TransactionService::class);

            foreach ($sales as $sData) {
                $uuid = $sData['uuid'] ?? (string) Str::uuid();
                $items = $sData['items'] ?? [];
                
                unset($sData['id']);
                unset($sData['items']);

                $customerUuid = $sData['customer_uuid'] ?? null;
                if ($customerUuid) {
                    $cloudCustomer = Customer::where('uuid', $customerUuid)->first();
                    if ($cloudCustomer) {
                        $sData['customer_id'] = $cloudCustomer->id;
                    }
                }
                unset($sData['customer_uuid']);

                $sale = Sale::where('uuid', $uuid)->first();
                if (!$sale) {
                    // Conflict protection: if invoice number already taken by another sale on cloud, assign next available number
                    if (Sale::where('invoice_no', $sData['invoice_no'])->exists()) {
                        $sData['invoice_no'] = \App\Models\InvoiceSeries::generateNextNo('INV');
                    }

                    $sale = new Sale($sData);
                    $sale->uuid = $uuid;
                    $sale->is_synced = 1;
                    $sale->save();

                    // Advance series counter on cloud
                    \App\Models\InvoiceSeries::incrementCounterForInvoice($sale->invoice_no);

                    // Create sale items
                    foreach ($items as $iData) {
                        unset($iData['id']);
                        unset($iData['sale_id']);
                        
                        $productCode = $iData['product_code'] ?? null;
                        if ($productCode) {
                            $cloudProduct = Product::where('item_code', $productCode)->first();
                            if ($cloudProduct) {
                                $iData['product_id'] = $cloudProduct->id;
                            }
                        }
                        unset($iData['product_code']);

                        $saleItem = new SaleItem($iData);
                        $saleItem->sale_id = $sale->id;
                        $saleItem->save();
                    }

                    // Process postings on the cloud
                    if ($sale->sale_status === 'posted') {
                        // 1. Deduct stock from warehouse
                        try {
                            $saleController->handleStockImpact($sale, 'out');
                        } catch (\Exception $e) {
                            Log::warning('Stock impact warning during sync: ' . $e->getMessage());
                        }
                        
                        // 2. Legacy Ledger posting
                        try {
                            $saleController->updateLedger($sale);
                        } catch (\Exception $e) {
                            Log::warning('Ledger posting warning during sync: ' . $e->getMessage());
                        }

                        // 3. Professional Ledger posting (Debit AR, Credit Sales)
                        try {
                            $custForVoucher = $sale->customer_relation ?? Customer::find($sale->customer_id);
                            if ($custForVoucher) {
                                $date = $sale->created_at ? $sale->created_at->format('Y-m-d') : date('Y-m-d');
                                $balanceService->createSaleVoucher(
                                    $custForVoucher,
                                    $sale->total_net,
                                    $sale->invoice_no,
                                    $date
                                );
                            }
                        } catch (\Exception $e) {
                            Log::warning('Sale voucher warning during sync: ' . $e->getMessage());
                        }

                        // 4. Auto Receipt (cash payment posting)
                        try {
                            $transactionService->createReceiptFromSale($sale);
                        } catch (\Exception $e) {
                            Log::warning('Receipt warning during sync: ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            // 2b. Find any customers on Cloud that Local does NOT have yet
            $cloudCustomersForLocal = [];
            if (!empty($knownCustomerUuids)) {
                Customer::whereNull('uuid')->orWhere('uuid', '')->each(function($c) {
                    $c->uuid = (string) Str::uuid();
                    $c->saveQuietly();
                });

                $cloudCustomersForLocal = Customer::whereNotIn('uuid', $knownCustomerUuids)
                    ->get()
                    ->toArray();
            }

            // 3. Find any sales on Cloud that Local does NOT have yet
            $cloudSalesForLocal = [];
            if (!empty($knownSaleUuids)) {
                $cloudSalesForLocal = Sale::whereNotIn('uuid', $knownSaleUuids)
                    ->with(['items.product', 'customer_relation'])
                    ->orderBy('id', 'asc')
                    ->get()
                    ->map(function($s) {
                        $sData = $s->toArray();
                        $sData['customer_uuid'] = $s->customer_relation ? $s->customer_relation->uuid : null;
                        $sData['items'] = $s->items->map(function($item) {
                            $iData = $item->toArray();
                            $iData['product_code'] = $item->product ? $item->product->item_code : null;
                            return $iData;
                        })->toArray();
                        return $sData;
                    })->toArray();
            }

            // 4. Return updated product stock quantities only for affected items
            $affectedProductIds = collect($cloudSalesForLocal)->pluck('items')->flatten(1)->pluck('product_id')->filter()->unique();
            $productStocks = [];
            if ($affectedProductIds->isNotEmpty()) {
                $productStocks = Product::whereIn('id', $affectedProductIds)->select('item_code', 'total_stock_qty')->get()->toArray();
            }

            // 5. Return updated customer balances for affected customers
            $affectedCustomerUuids = collect($cloudSalesForLocal)->pluck('customer_uuid')->filter()->unique();
            $customerBalances = [];
            if ($affectedCustomerUuids->isNotEmpty()) {
                $customerBalances = Customer::whereIn('uuid', $affectedCustomerUuids)->select('uuid', 'previous_balance')->get()->toArray();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Sync payload processed successfully.',
                'cloud_customers' => $cloudCustomersForLocal,
                'cloud_sales' => $cloudSalesForLocal,
                'product_stocks' => $productStocks,
                'customer_balances' => $customerBalances
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Sync error on cloud: ' . $ex->getMessage());
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }
}
