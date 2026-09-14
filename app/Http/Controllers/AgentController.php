<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Agent;
use App\Models\AgentLedger;
use App\Models\Sale;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * List all agents with commission summary.
     */
    public function index()
    {
        $agents = Agent::with('ledgers')->orderBy('name')->get()->map(function ($agent) {
            $agent->total_commission = (float) $agent->ledgers->sum('commission_amount');
            $agent->paid_commission = (float) $agent->ledgers->sum('payment_amount');
            $agent->outstanding = $agent->total_commission - $agent->paid_commission;

            return $agent;
        });

        $stats = [
            'total_agents' => $agents->count(),
            'active_agents' => $agents->where('status', 'active')->count(),
            'total_commission' => $agents->sum('total_commission'),
            'paid_commission' => $agents->sum('paid_commission'),
            'outstanding_commission' => $agents->sum('outstanding'),
        ];

        return view('admin_panel.agents.index', compact('agents', 'stats'));
    }

    /**
     * Store / update an agent (AJAX).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($request->edit_id) {
            $agent = Agent::findOrFail($request->edit_id);
            $agent->update($request->only('name', 'contact_number', 'address', 'status'));
            $message = 'Agent updated successfully';
        } else {
            $agent = Agent::create($request->only('name', 'contact_number', 'address', 'status') + ['status' => 'active']);
            $message = 'Agent created successfully';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'agent' => $agent]);
        }

        return back()->with('success', $message);
    }

    /**
     * Quick add an agent from the Sale screen (AJAX, returns JSON for dropdown update).
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $agent = Agent::create([
            'name' => $request->name,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'status' => 'active',
        ]);

        $agents = Agent::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'message' => 'Agent created successfully',
            'agent' => $agent,
            'agents' => $agents,
        ]);
    }

    /**
     * Return active agents as JSON for dropdowns.
     */
    public function getAgentsJson()
    {
        $agents = Agent::where('status', 'active')->orderBy('name')->get(['id', 'name']);

        return response()->json($agents);
    }

    /**
     * Get a single agent for edit modal.
     */
    public function edit($id)
    {
        return response()->json(Agent::findOrFail($id));
    }

    /**
     * Toggle agent status (active / inactive).
     */
    public function toggleStatus($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->status = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->save();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'status' => $agent->status]);
        }

        return back()->with('success', 'Agent status updated');
    }

    /**
     * Delete an agent.
     */
    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);

        $hasSales = Sale::where('agent_id', $id)->exists();
        if ($hasSales) {
            return back()->with('error', 'Cannot delete agent because sales are linked to this agent.');
        }

        // Also delete ledger entries
        AgentLedger::where('agent_id', $id)->delete();
        $agent->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Agent deleted successfully');
    }

    /**
     * Agent ledger view.
     */
    public function ledger($id)
    {
        $agent = Agent::findOrFail($id);

        $entries = AgentLedger::where('agent_id', $id)
            ->orderBy('id')
            ->get();

        $runningBalance = 0;
        $entries = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance = (float) $entry->balance;
            $entry->running_balance = $runningBalance;

            return $entry;
        });

        $totalCommission = $entries->sum('commission_amount');
        $totalPaid = $entries->sum('payment_amount');
        $outstanding = max(0, $totalCommission - $totalPaid);

        // Cash/Bank accounts for commission payment
        $accounts = Account::whereHas('head', function ($q) {
            $q->whereIn('name', ['Cash', 'Bank']);
        })->where('status', 1)
            ->orderBy('title')
            ->get();

        return view('admin_panel.agents.ledger', compact('agent', 'entries', 'totalCommission', 'totalPaid', 'outstanding', 'accounts'));
    }

    /**
     * Record a commission payment to the agent (reduces outstanding).
     */
    public function payCommission(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:255',
            'payment_account_id' => 'nullable|exists:accounts,id',
        ]);

        $agent = Agent::findOrFail($id);
        $amount = (float) $request->amount;

        $entry = $this->commissionService->recordCommissionPayment(
            $agent,
            $amount,
            'Commission Payment ' . now()->format('Y-m-d'),
            $request->remarks ?: 'Commission Paid to ' . $agent->name,
            $request->payment_account_id
        );

        if ($entry) {
            return back()->with('success', 'Commission payment of Rs ' . number_format($amount, 2) . ' recorded for ' . $agent->name);
        }

        return back()->with('error', 'Failed to record commission payment.');
    }

    /**
     * Get agent outstanding balance (JSON).
     */
    public function getBalance($id)
    {
        $agent = Agent::findOrFail($id);

        return response()->json([
            'total_commission' => $agent->total_commission,
            'paid_commission' => $agent->paid_commission,
            'outstanding' => $agent->outstanding_commission,
        ]);
    }
}