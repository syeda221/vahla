<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usertype = Auth::user()->usertype;
        $userId = Auth::id();

        if ($usertype == 'user') {
            return view('user_panel.dashboard', compact('userId'));
        } elseif ($usertype == 'admin') {
            // Counts
            $categoryCount = Auth::user()->can('categories.view') ? DB::table('categories')->count() : 0;
            $subcategoryCount = Auth::user()->can('subcategories.view') ? DB::table('subcategories')->count() : 0;
            $productCount = Auth::user()->can('products.view') ? DB::table('products')->count() : 0;
            $customerscount = Auth::user()->can('customers.view') ? DB::table('customers')->count() : 0;

            // Stats
            $totalPurchases = Auth::user()->can('purchases.view') ? DB::table('purchases')->sum('net_amount') : 0;
            $totalPurchaseReturns = Auth::user()->can('purchase.returns.view') ? DB::table('purchase_returns')->sum('net_amount') : 0;
            $totalSales = Auth::user()->can('sales.view') ? DB::table('sales')->where(function($q) { $q->whereNull('sale_type')->orWhere('sale_type', 'direct_sale'); })->where('sale_status', 'posted')->sum('total_net') : 0;
            $totalSalesReturns = Auth::user()->can('sales.returns.view') ? DB::table('sale_returns')->sum('net_amount') : 0;

            // Financial Summary (Accounting Based)
            $financialSummary = [];
            if (Auth::user()->can('purchases.view') || Auth::user()->can('sales.view')) {
                try {
                    $balanceService = app(\App\Services\BalanceService::class);
                    $fromDate = request('from_date', now()->startOfMonth()->format('Y-m-d'));
                    $toDate = request('to_date', now()->endOfMonth()->format('Y-m-d'));
                    $financialSummary = $balanceService->getFinancialSummary($fromDate, $toDate);
                } catch (\Exception $e) {
                     \Log::error("Dashboard Financial Summary Error: " . $e->getMessage());
                }
            }

            // ===== SALES REPORT CHARTS =====
            $salesChartStats = ['daily' => ['series' => [], 'categories' => []]];
            if (Auth::user()->can('sales.view')) {
                // DAILY (last 7 days - Billed Sales Only)
                $dailyLabels = collect(range(6, 0))->map(fn($i) => \Carbon\Carbon::today()->subDays($i)->format('Y-m-d'));
                $dailyData = $dailyLabels->map(function ($date) {
                    return DB::table('sales')
                        ->whereDate('created_at', $date)
                        ->where(function($q) {
                            $q->whereNull('sale_type')
                              ->orWhere('sale_type', 'direct_sale');
                        })
                        ->where('sale_status', 'posted')
                        ->sum('total_net');
                });

                // WEEKLY (This + Last 2 weeks - Billed Sales Only)
                $weeklyLabels = ['This Week', 'Last Week', '2 Weeks Ago'];
                $weeklyData = collect([0, 1, 2])->map(function ($i) {
                    $start = \Carbon\Carbon::now()->startOfWeek()->subWeeks($i);
                    $end = $start->copy()->endOfWeek();
                    return DB::table('sales')
                        ->whereBetween('created_at', [$start, $end])
                        ->where(function($q) {
                            $q->whereNull('sale_type')
                              ->orWhere('sale_type', 'direct_sale');
                        })
                        ->where('sale_status', 'posted')
                        ->sum('total_net');
                })->reverse()->values();

                // MONTHLY (Jan → Current month - Billed Sales Only)
                $months = range(1, \Carbon\Carbon::now()->month);
                $monthLabels = collect($months)->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('F'));
                $monthlyData = collect($months)->map(function ($month) {
                    return DB::table('sales')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', \Carbon\Carbon::now()->year)
                        ->where(function($q) {
                            $q->whereNull('sale_type')
                              ->orWhere('sale_type', 'direct_sale');
                        })
                        ->where('sale_status', 'posted')
                        ->sum('total_net');
                });

                $salesChartStats = [
                    'daily' => [
                        'categories' => $dailyLabels,
                        'series' => [['name' => 'Sales', 'data' => $dailyData]]
                    ],
                    'weekly' => [
                        'categories' => $weeklyLabels,
                        'series' => [['name' => 'Sales', 'data' => $weeklyData]]
                    ],
                    'monthly' => [
                        'categories' => $monthLabels,
                        'series' => [['name' => 'Sales', 'data' => $monthlyData]]
                    ]
                ];
            }

            // ===== PURCHASE CHARTS =====
            $purchaseChartStats = ['daily' => ['series' => [], 'categories' => []]];
            if (Auth::user()->can('purchases.view')) {
                // DAILY
                $purchaseDailyLabels = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i)->format('Y-m-d'));
                $purchaseDailySeries = [[
                    'name' => 'Purchases',
                    'data' => $purchaseDailyLabels->map(function ($date) {
                        return DB::table('purchases')
                            ->whereDate('created_at', $date)
                            ->sum('net_amount');
                    })
                ]];

                // WEEKLY
                $purchaseWeeklyLabels = ['This Week', 'Last Week', '2 Weeks Ago'];
                $purchaseWeeklySeries = [[
                    'name' => 'Purchases',
                    'data' => collect([0, 1, 2])->map(function ($i) {
                        $start = Carbon::now()->startOfWeek()->subWeeks($i);
                        $end = $start->copy()->endOfWeek();
                        return DB::table('purchases')
                            ->whereBetween('created_at', [$start, $end])
                            ->sum('net_amount');
                    })->reverse()->values()
                ]];

                // MONTHLY
                $months = range(1, Carbon::now()->month);
                $purchaseMonthLabels = collect($months)->map(fn($m) => Carbon::create()->month($m)->format('F'));
                $purchaseMonthlySeries = [[
                    'name' => 'Purchases',
                    'data' => collect($months)->map(function ($month) {
                        return DB::table('purchases')
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->sum('net_amount');
                    })
                ]];

                $purchaseChartStats = [
                    'daily' => [
                        'categories' => $purchaseDailyLabels,
                        'series' => $purchaseDailySeries
                    ],
                    'weekly' => [
                        'categories' => $purchaseWeeklyLabels,
                        'series' => $purchaseWeeklySeries
                    ],
                    'monthly' => [
                        'categories' => $purchaseMonthLabels,
                        'series' => $purchaseMonthlySeries
                    ]
                ];
            }

            // ===== PAYMENT IN / OUT STATS =====
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();

            // 1. Payment IN (Today)
            $v2ReceiptsMonth = DB::table('voucher_masters')->where('voucher_type', 'receipt')->whereBetween('date', [$todayStart, $todayEnd])->sum('total_amount');
            $v1ReceiptsMonth = DB::table('receipts_vouchers')->whereBetween('receipt_date', [$todayStart, $todayEnd])->sum('total_amount');
            $custPaymentsMonth = DB::table('customer_payments')->whereBetween('payment_date', [$todayStart, $todayEnd])->sum('amount');
            $paymentInMonth = $v2ReceiptsMonth + $v1ReceiptsMonth + $custPaymentsMonth;

            // 2. Payment IN (Overall)
            $v2ReceiptsAll = DB::table('voucher_masters')->where('voucher_type', 'receipt')->sum('total_amount');
            $v1ReceiptsAll = DB::table('receipts_vouchers')->sum('total_amount');
            $custPaymentsAll = DB::table('customer_payments')->sum('amount');
            $paymentInOverall = $v2ReceiptsAll + $v1ReceiptsAll + $custPaymentsAll;

            // 3. Payment OUT (Today)
            $v2PaymentsMonth = DB::table('voucher_masters')->whereIn('voucher_type', ['payment', 'expense'])->whereBetween('date', [$todayStart, $todayEnd])->sum('total_amount');
            $v1PaymentsMonth = DB::table('payment_vouchers')->whereBetween('receipt_date', [$todayStart, $todayEnd])->sum('total_amount');
            $v1ExpensesMonth = DB::table('expense_vouchers')->whereBetween('entry_date', [$todayStart, $todayEnd])->sum('total_amount');
            $vendorPaymentsMonth = DB::table('vendor_payments')->whereBetween('payment_date', [$todayStart, $todayEnd])->sum('amount');
            $paymentOutMonth = $v2PaymentsMonth + $v1PaymentsMonth + $v1ExpensesMonth + $vendorPaymentsMonth;

            // 4. Payment OUT (Overall)
            $v2PaymentsAll = DB::table('voucher_masters')->whereIn('voucher_type', ['payment', 'expense'])->sum('total_amount');
            $v1PaymentsAll = DB::table('payment_vouchers')->sum('total_amount');
            $v1ExpensesAll = DB::table('expense_vouchers')->sum('total_amount');
            $vendorPaymentsAll = DB::table('vendor_payments')->sum('amount');
            $paymentOutOverall = $v2PaymentsAll + $v1PaymentsAll + $v1ExpensesAll + $vendorPaymentsAll;

            // ===== TOP 10 PRODUCTS & CUSTOMERS (Needs Sales Permission) =====
            $topProducts = collect();
            $topCustomers = collect();

            if (Auth::user()->can('sales.view')) {
                $topProducts = DB::table('sale_items')
                    ->select('product_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(total) as total_revenue'))
                    ->groupBy('product_name')
                    ->orderByDesc('total_qty')
                    ->limit(10)
                    ->get();

                $topCustomers = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->select('customers.customer_name', DB::raw('COUNT(sales.id) as total_orders'), DB::raw('SUM(sales.total_net) as total_sales'))
                    ->groupBy('customers.id', 'customers.customer_name')
                    ->orderByDesc('total_sales')
                    ->limit(10)
                    ->get();
            }

            $cashAndBankAccounts = \App\Models\Account::whereHas('head', function ($query) {
                $query->whereIn('name', ['Cash', 'Bank']);
            })->where('status', 1)->get();

            $totalCashAndBankBalance = $cashAndBankAccounts->sum('current_balance');

            // ===== LOW STOCK PRODUCTS =====
            $lowStockProducts = collect();
            if (Auth::user()->can('products.view')) {
                $lowStockProducts = \App\Models\Product::withSum('warehouseStocks', 'total_pieces')
                    ->whereNotNull('alert_carton_quantity')
                    ->where('is_active', 1)
                    ->get()
                    ->filter(function ($p) {
                        $stock = (float) ($p->warehouse_stocks_sum_total_pieces ?? 0);
                        $ppb = $p->pieces_per_box > 0 ? $p->pieces_per_box : 1;
                        $cartons = floor($stock / $ppb);
                        $p->current_cartons = $cartons;
                        return $cartons < $p->alert_carton_quantity;
                    })
                    ->sortBy(function ($p) {
                        return $p->current_cartons - $p->alert_carton_quantity; // most critical (most negative) first
                    })
                    ->take(15)
                    ->values();
            }

            $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
            $endOfMonth   = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');

            // Strictly Billed Sales for This Month (Excluding Quotations, Sales Orders, Drafts, DCs)
            $salesThisMonthQuery = DB::table('sales')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where(function($q) {
                    $q->whereNull('sale_type')
                      ->orWhere('sale_type', 'direct_sale');
                })
                ->where('sale_status', 'posted');

            $salesThisMonth = (float) $salesThisMonthQuery->sum('total_net');
            $salesThisMonthCount = (int) $salesThisMonthQuery->count();

            $purchasesThisMonth = DB::table('purchases')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('net_amount');

            // Strictly Billed Sales for Today (Excluding Quotations, Sales Orders, Drafts, DCs)
            $salesTodayQuery = DB::table('sales')
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->where(function($q) {
                    $q->whereNull('sale_type')
                      ->orWhere('sale_type', 'direct_sale');
                })
                ->where('sale_status', 'posted');

            $salesToday = (float) $salesTodayQuery->sum('total_net');
            $salesTodayCount = (int) $salesTodayQuery->count();
            $salesTodayCash = (float) $salesTodayQuery->sum('cash');

            $purchasesToday = DB::table('purchases')
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->sum('net_amount');

            // Calculate real profit for current month (matching Profit & Loss Report)
            $saleItemsThisMonth = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
                ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
                ->where('sales.sale_status', '!=', 'cancelled')
                ->select(
                    'sale_items.price',
                    'sale_items.color',
                    'sale_items.total_pieces',
                    'sale_items.total',
                    'sale_items.is_manual',
                    'sale_items.purchase_price as manual_purchase_price',
                    'sales.total_extradiscount',
                    'sales.total_bill_amount',
                    'products.size_mode',
                    'products.pieces_per_m2',
                    'products.purchase_price_per_m2',
                    'products.purchase_price_per_piece'
                )->get();

            $totalCostThisMonth = 0;
            $totalRevenueThisMonth = 0;
            foreach ($saleItemsThisMonth as $si) {
                $qty = (float)($si->total_pieces ?? 1);
                
                if ($si->is_manual) {
                    $purchPrice = (float) $si->manual_purchase_price;
                } else {
                    $purchPrice = 0;
                    if (!empty($si->color)) {
                        $decoded = base64_decode($si->color, true);
                        $json = $decoded ? json_decode($decoded, true) : json_decode($si->color, true);
                        if (is_array($json)) {
                            $purchPrice = (float)($json['purch_price'] ?? 0);
                        }
                    }

                    if ($purchPrice <= 0) {
                        if ($si->size_mode === 'by_size') {
                            $m2PerPiece = (float) ($si->pieces_per_m2 ?? 0);
                            $purchPerM2 = (float) ($si->purchase_price_per_m2 ?? 0);
                            $purchPrice = $m2PerPiece * $purchPerM2;
                        } else {
                            $purchPrice = (float) ($si->purchase_price_per_piece ?? 0);
                        }
                    }
                }

                $itemNet = (float) $si->total;
                if ($si->total_bill_amount > 0 && $si->total_extradiscount > 0) {
                    $proportion = $itemNet / (float) $si->total_bill_amount;
                    $itemNet -= ($si->total_extradiscount * $proportion);
                }

                $totalRevenueThisMonth += $itemNet;
                $totalCostThisMonth    += $purchPrice * $qty;
            }

            // Accurately deduct any returned items in current month
            $saleReturnsThisMonth = DB::table('sale_return_items')
                ->join('sale_returns', 'sale_returns.id', '=', 'sale_return_items.sale_return_id')
                ->leftJoin('products', 'products.id', '=', 'sale_return_items.product_id')
                ->whereBetween('sale_returns.created_at', [$startOfMonth, $endOfMonth])
                ->select(
                    'sale_return_items.qty',
                    'sale_return_items.line_total',
                    'sale_return_items.color',
                    'sale_return_items.is_manual',
                    'sale_return_items.purchase_price as manual_purchase_price',
                    'products.size_mode',
                    'products.pieces_per_m2',
                    'products.purchase_price_per_m2',
                    'products.purchase_price_per_piece'
                )->get();

            foreach ($saleReturnsThisMonth as $sr) {
                $qty = (float)($sr->qty ?? 1);
                
                if ($sr->is_manual) {
                    $purchPrice = (float) $sr->manual_purchase_price;
                } else {
                    $purchPrice = 0;
                    if (!empty($sr->color)) {
                        $decoded = base64_decode($sr->color, true);
                        $json = $decoded ? json_decode($decoded, true) : json_decode($sr->color, true);
                        if (is_array($json)) {
                            $purchPrice = (float)($json['purch_price'] ?? 0);
                        }
                    }

                    if ($purchPrice <= 0) {
                        if ($sr->size_mode === 'by_size') {
                            $m2PerPiece = (float) ($sr->pieces_per_m2 ?? 0);
                            $purchPerM2 = (float) ($sr->purchase_price_per_m2 ?? 0);
                            $purchPrice = $m2PerPiece * $purchPerM2;
                        } else {
                            $purchPrice = (float) ($sr->purchase_price_per_piece ?? 0);
                        }
                    }
                }

                $totalRevenueThisMonth -= (float) $sr->line_total;
                $totalCostThisMonth    -= $purchPrice * $qty;
            }
            $profitThisMonth = $totalRevenueThisMonth - $totalCostThisMonth;

            // Accurate Accounting Receivables & Payables in Single SQL Queries (Instant Execution)
            $totalReceivables = DB::table('journal_entries')
                ->where('party_type', \App\Models\Customer::class)
                ->selectRaw('COALESCE(SUM(debit) - SUM(credit), 0) as total')
                ->value('total') ?? 0;

            $apId = app(\App\Services\BalanceService::class)->getAccountsPayableId();
            $totalPayables = DB::table('journal_entries')
                ->where('party_type', \App\Models\Vendor::class)
                ->where('account_id', $apId)
                ->selectRaw('COALESCE(SUM(credit) - SUM(debit), 0) as total')
                ->value('total') ?? 0;

            // Expenses Today
            $expensesTodayV1 = DB::table('expense_vouchers')->whereDate('entry_date', \Carbon\Carbon::today())->sum('total_amount');
            $expensesTodayV2 = DB::table('voucher_masters')->where('voucher_type', 'expense')->whereDate('date', \Carbon\Carbon::today())->sum('total_amount');
            $expensesToday = $expensesTodayV1 + $expensesTodayV2;

            // Total Stock Valuation in Single SQL Query
            $totalStockValue = DB::table('warehouse_stocks')
                ->join('products', 'products.id', '=', 'warehouse_stocks.product_id')
                ->selectRaw('COALESCE(SUM(warehouse_stocks.total_pieces * (CASE WHEN products.size_mode = "by_size" THEN COALESCE(products.pieces_per_m2, 0) * COALESCE(products.purchase_price_per_m2, 0) ELSE COALESCE(products.purchase_price_per_piece, 0) END)), 0) as total')
                ->value('total') ?? 0;

            $vendorCount = DB::table('vendors')->count();
            $employeeCount = \Illuminate\Support\Facades\Schema::hasTable('hr_employees') ? DB::table('hr_employees')->count() : DB::table('users')->count();

            // Sales by Category (Current Month)
            $salesByCategory = DB::table('sale_items')
                ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                ->join('products', 'products.id', '=', 'sale_items.product_id')
                ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
                ->where('sales.sale_status', '!=', 'cancelled')
                ->select(DB::raw('COALESCE(categories.name, "General") as category_name'), DB::raw('SUM(sale_items.total) as total_amount'))
                ->groupBy('category_name')
                ->orderByDesc('total_amount')
                ->limit(5)
                ->get();

            // Expenses This Month
            $expensesThisMonthV1 = DB::table('expense_vouchers')->whereBetween('entry_date', [substr($startOfMonth, 0, 10), substr($endOfMonth, 0, 10)])->sum('total_amount');
            $expensesThisMonthV2 = DB::table('voucher_masters')->where('voucher_type', 'expense')->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total_amount');
            $expensesThisMonth = $expensesThisMonthV1 + $expensesThisMonthV2;

            $grossProfitThisMonth = $profitThisMonth;
            $netProfitThisMonth = $grossProfitThisMonth - $expensesThisMonth;

            // Expense Breakdown by type (column name is 'type', not 'expense_type')
            $expenseBreakdown = DB::table('expense_vouchers')
                ->select('type as name', DB::raw('SUM(total_amount) as total'))
                ->groupBy('type')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            // Recent Activities Feed
            $recentSales = DB::table('sales')->latest()->limit(3)->get()->map(function($s) {
                return [
                    'icon' => 'fa-receipt text-success',
                    'bg' => '#ecfdf5',
                    'title' => 'New Sale Invoice #' . $s->invoice_no,
                    'subtitle' => 'Sale Transaction',
                    'amount' => 'Rs ' . number_format($s->total_net, 0),
                    'time' => \Carbon\Carbon::parse($s->created_at)->diffForHumans()
                ];
            });

            $recentPurchases = DB::table('purchases')->latest()->limit(2)->get()->map(function($p) {
                return [
                    'icon' => 'fa-cart-shopping text-primary',
                    'bg' => '#eef2ff',
                    'title' => 'Purchase Bill #' . $p->invoice_no,
                    'subtitle' => 'Procurement',
                    'amount' => 'Rs ' . number_format($p->net_amount, 0),
                    'time' => \Carbon\Carbon::parse($p->created_at)->diffForHumans()
                ];
            });

            $recentActivities = $recentSales->concat($recentPurchases)->values();

            return view('admin_panel.dashboard', compact(
                'categoryCount',
                'subcategoryCount',
                'productCount',
                'customerscount',
                'vendorCount',
                'employeeCount',
                'totalPurchases',
                'totalPurchaseReturns',
                'totalSales',
                'totalSalesReturns',
                'salesChartStats',
                'purchaseChartStats',
                'financialSummary',
                'paymentInMonth',
                'paymentInOverall',
                'paymentOutMonth',
                'paymentOutOverall',
                'topProducts',
                'topCustomers',
                'cashAndBankAccounts',
                'totalCashAndBankBalance',
                'lowStockProducts',
                'salesToday',
                'salesTodayCount',
                'salesTodayCash',
                'salesThisMonth',
                'salesThisMonthCount',
                'purchasesToday',
                'purchasesThisMonth',
                'profitThisMonth',
                'totalRevenueThisMonth',
                'totalCostThisMonth',
                'totalReceivables',
                'totalPayables',
                'expensesToday',
                'expensesThisMonth',
                'grossProfitThisMonth',
                'netProfitThisMonth',
                'totalStockValue',
                'salesByCategory',
                'expenseBreakdown',
                'recentActivities'
            ));
        } else {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
    }
}
