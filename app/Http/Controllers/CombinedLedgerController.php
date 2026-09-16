<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\CustomerLedger;
use App\Models\VendorLedger;

class CombinedLedgerController extends Controller
{
    public function index(Request $request)
    {
        $customerId = $request->customer_id;
        $fromDate = $request->from_date ?: '2000-01-01';
        $toDate = $request->to_date ?: date('Y-m-d');

        // Get customers who are linked to vendors
        $dualCustomers = Customer::whereNotNull('linked_vendor_id')->orderBy('customer_name')->get();

        $combinedLedger = [];
        $openingBalance = 0;
        $closingBalance = 0;
        $customer = null;
        $vendor = null;

        if ($customerId) {
            $customer = Customer::find($customerId);
            
            if ($customer && $customer->linked_vendor_id) {
                $vendor = Vendor::find($customer->linked_vendor_id);
                $balanceService = app(\App\Services\BalanceService::class);
                
                // Get Ledgers from BalanceService which has accurate debits/credits via JournalEntry
                $customerData = $balanceService->getCustomerLedger($customer->id, $fromDate, $toDate);
                $vendorData = $balanceService->getVendorLedger($vendor->id, $fromDate, $toDate);
                
                $custOpBal = $customerData['opening_balance'] ?? 0;
                $vendOpBal = $vendorData['opening_balance'] ?? 0;
                
                // Opening Balance Net
                $openingBalance = $custOpBal - $vendOpBal;
                $runningBalance = $openingBalance;
                
                // Map customer transactions
                $customerLogs = collect($customerData['transactions'] ?? [])->map(function($t) {
                    return (object) [
                        'date' => $t['date'],
                        'created_at' => \Carbon\Carbon::parse($t['date'])->toDateTimeString(), // For fallback sorting
                        'source_type' => 'Customer',
                        'description' => $t['description'],
                        'debit' => $t['debit'],
                        'credit' => $t['credit']
                    ];
                });

                // Map vendor transactions
                $vendorLogs = collect($vendorData['transactions'] ?? [])->map(function($t) {
                    return (object) [
                        'date' => $t['date'],
                        'created_at' => \Carbon\Carbon::parse($t['date'])->toDateTimeString(),
                        'source_type' => 'Vendor',
                        'description' => $t['description'],
                        'debit' => $t['debit'],
                        'credit' => $t['credit']
                    ];
                });

                // Merge and Sort by Date
                $merged = $customerLogs->concat($vendorLogs)->sortBy(function ($item) {
                    return $item->date . ' ' . $item->created_at;
                })->values();
                
                foreach ($merged as $entry) {
                    $dr = (float)($entry->debit ?? 0);
                    $cr = (float)($entry->credit ?? 0);
                    
                    if ($entry->source_type === 'Customer') {
                        $runningBalance += $dr; // Increases what they owe
                        $runningBalance -= $cr; // Decreases what they owe
                    } else {
                        // Vendor Ledger: Credit is what we owe them, Debit is what we paid them
                        $runningBalance -= $cr; // Decreases net receivable (increases our liability)
                        $runningBalance += $dr; // Increases net receivable (decreases our liability)
                    }
                    
                    $entry->running_balance = $runningBalance;
                    $combinedLedger[] = $entry;
                }
                
                $closingBalance = $runningBalance;
            }
        }

        return view('admin_panel.customers.combined_ledger', compact(
            'dualCustomers', 
            'combinedLedger', 
            'customer', 
            'vendor',
            'openingBalance',
            'closingBalance'
        ));
    }
}
