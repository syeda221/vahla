<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Account;
use App\Services\ChequeAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChequeController extends Controller
{
    protected $alertService;

    public function __construct(ChequeAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Display Cheque Register / Management Dashboard.
     */
    public function index(Request $request)
    {
        // Run checks to ensure latest auto-deductions and alerts are triggered
        try {
            $this->alertService->processCheques();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Cheque process error on index: " . $e->getMessage());
        }

        $activeTab = $request->get('tab', 'pending');
        $query = Cheque::with(['customer', 'vendor', 'bankAccount', 'clearedAccount', 'voucherMaster'])
            ->orderBy('cheque_date', 'asc')
            ->orderBy('id', 'desc');

        if ($activeTab === 'inward') {
            $query->where('type', 'inward')->where('status', 'pending');
        } elseif ($activeTab === 'outward') {
            $query->where('type', 'outward')->where('status', 'pending');
        } elseif ($activeTab === 'cleared') {
            $query->where('status', 'cleared');
        } elseif ($activeTab === 'bounced') {
            $query->where('status', 'bounced');
        } elseif ($activeTab === 'pending') {
            $query->where('status', 'pending');
        }

        // Search filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('cheque_no', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhereHas('customer', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('vendor', function ($vq) use ($s) {
                      $vq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        $cheques = $query->paginate(25)->withQueryString();

        // Calculate KPI summaries
        $stats = [
            'pending_inward_count' => Cheque::where('type', 'inward')->where('status', 'pending')->count(),
            'pending_inward_amount' => (float) Cheque::where('type', 'inward')->where('status', 'pending')->sum('amount'),
            'pending_outward_count' => Cheque::where('type', 'outward')->where('status', 'pending')->count(),
            'pending_outward_amount' => (float) Cheque::where('type', 'outward')->where('status', 'pending')->sum('amount'),
            'cleared_count' => Cheque::where('status', 'cleared')->count(),
            'cleared_amount' => (float) Cheque::where('status', 'cleared')->sum('amount'),
            'bounced_count' => Cheque::where('status', 'bounced')->count(),
        ];

        // Active Bank & Cash Accounts for clearance modal
        $destinationAccounts = Account::where('status', 1)
            ->where(function ($q) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%cash%'])
                  ->orWhereRaw('LOWER(title) LIKE ?', ['%bank%'])
                  ->orWhereIn('head_id', [1, 2]);
            })
            ->whereNotIn('account_code', ['AR', 'AP', 'SALES', 'PURCHASE', 'CHQ-001', 'CHQ-002'])
            ->orderBy('title')
            ->get();

        return view('admin_panel.cheques.index', compact(
            'cheques',
            'stats',
            'activeTab',
            'destinationAccounts'
        ));
    }

    /**
     * Clear / Deposit an Inward Cheque into Bank or Cash.
     */
    public function clearInward(Request $request, $id)
    {
        $request->validate([
            'destination_account_id' => 'required|exists:accounts,id',
            'cleared_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $cheque = Cheque::findOrFail($id);

        try {
            $this->alertService->clearInwardCheque(
                $cheque,
                (int) $request->destination_account_id,
                $request->cleared_date,
                $request->remarks
            );

            return response()->json([
                'success' => true,
                'message' => "Cheque #{$cheque->cheque_no} successfully cleared and deposited.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mark a cheque as Bounced.
     */
    public function bounce(Request $request, $id)
    {
        $request->validate([
            'bounce_reason' => 'required|string|max:500',
        ]);

        $cheque = Cheque::findOrFail($id);

        try {
            $this->alertService->bounceCheque($cheque, $request->bounce_reason);

            return response()->json([
                'success' => true,
                'message' => "Cheque #{$cheque->cheque_no} marked as bounced.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Manual trigger to run all checks and return stats.
     */
    public function runChecks()
    {
        try {
            $res = $this->alertService->processCheques();

            $activeAlerts = $res['active_low_balance_alerts'];
            $newAlerts = $res['low_balance_notified'];
            $autoDeducted = $res['auto_deducted'];
            $dueNotified = $res['due_notified'];

            $summaryLines = [];
            if ($activeAlerts > 0) {
                $summaryLines[] = "⚠️ {$activeAlerts} Cheque(s) have Low Bank Balance warning!";
            } else {
                $summaryLines[] = "✅ Bank balances are sufficient for all pending cheques.";
            }

            if ($autoDeducted > 0) {
                $summaryLines[] = "💰 {$autoDeducted} Cheque(s) auto-deducted today.";
            }
            if ($dueNotified > 0) {
                $summaryLines[] = "📥 {$dueNotified} Customer cheque(s) matured today.";
            }
            if ($newAlerts > 0) {
                $summaryLines[] = "🔔 {$newAlerts} new notification(s) sent.";
            }

            return response()->json([
                'success' => true,
                'message' => implode("\n", $summaryLines),
                'active_alerts' => $activeAlerts,
                'details' => $res['details'],
                'data' => $res,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
