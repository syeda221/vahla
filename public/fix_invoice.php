<?php
// fix_invoice.php
$basePath = file_exists(__DIR__ . '/../bootstrap/app.php') ? __DIR__ . '/..' : __DIR__;
require $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$inv = $_GET['inv'] ?? '';
$preview = isset($_GET['preview']) && $_GET['preview'] == 1;

if (empty($inv)) {
    die("<h3 style='font-family:sans-serif;'>Bhai pehle link mein invoice number likhein. <br><br>Example: <b>?inv=8&preview=1</b></h3>");
}

use App\Models\Sale;
use App\Http\Controllers\SaleController;
use App\Services\BalanceService;
use Illuminate\Support\Facades\DB;

// Invoice number se sale dhondain
$sale = Sale::where('invoice_no', $inv)->orWhere('id', $inv)->first();

if (!$sale) {
    die("<h3 style='font-family:sans-serif; color:red;'>Error: Sale / Invoice '{$inv}' system mein nahi mili.</h3>");
}

echo "<div style='font-family:sans-serif; margin: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 8px;'>";
echo "<h2>Invoice Fixer</h2>";
echo "<b>Found Sale Invoice:</b> {$sale->invoice_no} <br>";
echo "<b>Customer ID:</b> {$sale->customer_id} <br>";
echo "<b>Sale Total Amount:</b> Rs. {$sale->total_net} <br>";
echo "<b>Sale Date:</b> {$sale->created_at} <br><hr>";

if ($preview) {
    echo "<h3 style='color:orange;'>PREVIEW MODE (Check)</h3>";
    echo "<p>System yeh tabdeeliyan karega:</p>";
    echo "<ul>";
    echo "<li>Pehlay agar koi aadhi-adhuri corrupted entry bachi hui hai toh usay clean karega.</li>";
    echo "<li>Customer ke Ledger mein <b>Rs. {$sale->total_net}</b> ki nayi entry theek balance ke sath daalay ga.</li>";
    echo "<li>Journal Entries (Accounts Receivable aur Sales Revenue) dobara generate karega.</li>";
    echo "</ul>";
    echo "<br><a href='?inv={$inv}' style='background: green; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Bismillah Parh Kar Apply Karein</a>";
    echo "</div>";
    exit;
}

DB::beginTransaction();
try {
    // Purani kharab entry clean karna
    $vouchers = \App\Models\VoucherMaster::where('remarks', 'like', "%#{$sale->invoice_no}%")->get();
    if($vouchers->count() > 0) {
        $journalService = app(\App\Services\JournalEntryService::class);
        foreach ($vouchers as $voucher) {
            $journalService->reverseEntriesForSource($voucher);
            \App\Models\VoucherDetail::where('voucher_master_id', $voucher->id)->delete();
            $voucher->delete();
        }
    }
    
    $ledgerEntries = \App\Models\CustomerLedger::where('customer_id', $sale->customer_id)
          ->where('description', 'like', "%#{$sale->invoice_no}%")->get();
    if($ledgerEntries->count() > 0) {
        foreach ($ledgerEntries as $entry) {
            $entry->delete();
        }
    }

    // Nayi Entry (Legacy Ledger)
    $controller = app(SaleController::class);
    $controller->updateLedger($sale);
    echo "✅ Customer Ledger ki nayi entry lag gayi.<br><br>";

    // Nayi Entry (Professional Ledger / Voucher)
    $balanceService = app(BalanceService::class);
    $customer = $sale->customer_relation ?? \App\Models\Customer::find($sale->customer_id);
    
    if ($customer) {
        $date = $sale->created_at ? $sale->created_at->format('Y-m-d') : date('Y-m-d');
        $balanceService->createSaleVoucher($customer, $sale->total_net, $sale->invoice_no, $date);
        echo "✅ Journal entries aur Voucher generate ho gaye.<br><br>";
    }

    DB::commit();
    echo "<h3 style='color:green'>Success! Ledger aur previous balance bilkul theek ho gaya hai! 🎉</h3>";
    echo "<p>Ab aap system mein ja kar customer ka khata (ledger) check kar sakte hain.</p>";
} catch (\Exception $e) {
    DB::rollBack();
    echo "<b style='color:red'>Error:</b> " . $e->getMessage();
}
echo "</div>";
