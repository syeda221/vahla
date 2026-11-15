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
     * Runs on the local server instance to push data to the cloud.
     */
    public function syncToCloud(Request $request)
    {
        $cloudSyncUrl = rtrim(env('CLOUD_SYNC_URL', 'https://test.binsultansweet.com'), '/');
        $syncToken = env('SYNC_TOKEN', 'trendhub_sync_default_token_2026');

        // 1. Fetch unsynced customers
        $customers = Customer::where('is_synced', 0)->get()->map(function($c) {
            return $c->toArray();
        })->toArray();

        // 2. Fetch unsynced sales with items
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

        if (empty($customers) && empty($sales)) {
            return response()->json([
                'status' => 'success',
                'message' => 'All local data is already fully synchronized with the cloud! No pending bills found.',
                'synced_sales' => 0,
                'synced_customers' => 0
            ]);
        }

        try {
            // Send payload to cloud via HTTP Client (withoutVerifying to bypass local SSL errors)
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->withHeaders(['X-Sync-Token' => $syncToken])
                ->post($cloudSyncUrl . '/api/receive-sync', [
                    'customers' => $customers,
                    'sales' => $sales
                ]);

            if ($response->successful()) {
                $resData = $response->json();
                if (isset($resData['status']) && $resData['status'] === 'success') {
                    // Update local records to synced
                    if (!empty($customers)) {
                        Customer::whereIn('uuid', collect($customers)->pluck('uuid'))->update(['is_synced' => 1]);
                    }
                    if (!empty($sales)) {
                        Sale::whereIn('uuid', collect($sales)->pluck('uuid'))->update(['is_synced' => 1]);
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Cloud synchronization completed successfully! ' . count($sales) . ' sales and ' . count($customers) . ' customers synced.',
                        'synced_sales' => count($sales),
                        'synced_customers' => count($customers)
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
     * Runs on the cloud server instance to receive pushed data.
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

        DB::beginTransaction();
        try {
            // Process customers
            foreach ($customers as $cData) {
                $uuid = $cData['uuid'] ?? (string) Str::uuid();
                unset($cData['id']);
                
                $customer = Customer::where('uuid', $uuid)->first();
                if (!$customer) {
                    // Prevent duplicate customer_id collision on cloud
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

            // Process sales
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
                    // Resolve invoice_no collision
                    if (isset($sData['invoice_no']) && Sale::where('invoice_no', $sData['invoice_no'])->exists()) {
                        $sData['invoice_no'] = $sData['invoice_no'] . '-OFF';
                        if (Sale::where('invoice_no', $sData['invoice_no'])->exists()) {
                            $sData['invoice_no'] = 'INV-' . date('YmdHis') . '-' . rand(10, 99);
                        }
                    }

                    $sale = new Sale($sData);
                    $sale->uuid = $uuid;
                    $sale->is_synced = 1;
                    $sale->save();

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
            return response()->json(['status' => 'success', 'message' => 'Sync payload processed successfully.']);
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Sync error on cloud: ' . $ex->getMessage());
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * Cloud endpoint to pull master data (products and prices) down to local PC
     */
    public function pullMasterData(Request $request)
    {
        $token = $request->header('X-Sync-Token') ?? $request->input('sync_token');
        if ($token !== env('SYNC_TOKEN', 'trendhub_sync_default_token_2026')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized sync request.'], 401);
        }

        $lastUpdated = $request->input('last_updated');
        $query = Product::select('item_code', 'item_name', 'purchase_price_per_piece', 'price_per_piece', 'wholesale_price', 'sale_price_per_box', 'total_stock_qty', 'updated_at');
        if ($lastUpdated) {
            $query->where('updated_at', '>=', $lastUpdated);
        }
        $products = $query->limit(500)->get();

        return response()->json([
            'status' => 'success',
            'products' => $products
        ]);
    }
}
