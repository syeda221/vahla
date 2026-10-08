<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountHead;
use App\Models\AccountHistory;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class AccountsHeadController extends Controller
{
    public function index()
    {
        $heads = AccountHead::all();

        // Exclude system control accounts (AR, AP, SALES, PURCHASE, DISCOUNT, TAX)
        $excludedCodes = ['AR', 'AP', 'SALES', 'PURCHASE', 'GEN-EXP', 'DISC-EXP', 'DISC-INC', 'TAX-REC', 'WHT-PAY'];
        $excludedTitles = [
            'Accounts Receivable',
            'Accounts Payable',
            'Sales Revenue',
            'Purchase Expense',
            'General Expense',
            'Discount Allowed',
            'Discount Received',
            'Advance Income Tax / WHT Deducted',
            'Withholding Tax Payable'
        ];

        $accounts = Account::with(['head', 'histories.user'])
            ->whereNotIn('account_code', $excludedCodes)
            ->whereNotIn('title', $excludedTitles)
            ->orderBy('id', 'asc')
            ->get();

        return view('admin_panel.chart_of_accounts', compact('heads', 'accounts'));
    }

    public function storeHead(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:account_heads,name',
        ]);

        AccountHead::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Account Head added successfully!');
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'title' => 'required',
            'opening_balance' => 'required|numeric',
            'type' => 'required',
        ]);

        $initialBalance = (float) $request->opening_balance;

        $account = Account::create([
            'head_id' => $request->head_id,
            'title' => $request->title,
            'opening_balance' => $initialBalance,
            'current_balance' => $initialBalance, // Sync current balance initially
            'type' => $request->type,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        $account->account_code = 'ACC-'.str_pad($account->id, 4, '0', STR_PAD_LEFT);
        $account->save();

        // Log initial balance history
        AccountHistory::create([
            'account_id' => $account->id,
            'old_balance' => 0,
            'new_balance' => $initialBalance,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'User',
            'note' => 'Account created with initial balance: Rs ' . number_format($initialBalance, 2),
        ]);

        return back()->with('success', 'Account added successfully!');
    }

    public function updateAccount(Request $request, $id)
    {
        $request->validate([
            'head_id' => 'required|exists:account_heads,id',
            'title' => 'required',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'type' => 'required',
        ]);

        $account = Account::findOrFail($id);

        $oldOpening = (float) $account->opening_balance;
        $newOpening = (float) $request->opening_balance;

        $oldCurrent = (float) $account->current_balance;
        $newCurrent = (float) $request->current_balance;

        $account->title = $request->title;
        $account->head_id = $request->head_id;
        $account->type = $request->type;
        $account->opening_balance = $newOpening;
        $account->current_balance = $newCurrent;
        $account->status = $request->has('status') ? 1 : 0;

        $account->save();

        // Create detailed audit log entry if opening balance, current balance changed or note provided
        if ($oldOpening != $newOpening || $oldCurrent != $newCurrent || $request->filled('note')) {
            $user = auth()->user();

            $noteParts = [];
            if ($oldCurrent != $newCurrent) {
                $diff = $newCurrent - $oldCurrent;
                $diffStr = ($diff >= 0 ? '+' : '') . number_format($diff, 2);
                $noteParts[] = "Current Balance: Rs " . number_format($oldCurrent, 2) . " -> Rs " . number_format($newCurrent, 2) . " (Diff: " . $diffStr . ")";
            }
            if ($oldOpening != $newOpening) {
                $noteParts[] = "Opening: Rs " . number_format($oldOpening, 2) . " -> Rs " . number_format($newOpening, 2);
            }
            if ($request->filled('note')) {
                $noteParts[] = "Reason: " . $request->note;
            }

            AccountHistory::create([
                'account_id' => $account->id,
                'old_balance' => $oldCurrent,
                'new_balance' => $newCurrent,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'User',
                'note' => implode(' | ', $noteParts),
            ]);
        }

        return back()->with('success', 'Account "' . $account->title . '" ledger balance updated successfully!');
    }

    public function showLedger($id, Request $request)
    {
        $account = Account::findOrFail($id);

        $query = JournalEntry::where('account_id', $id)
            ->with('party')
            ->orderBy('entry_date', 'asc')
            ->orderBy('id', 'asc');

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('entry_date', [$request->from_date, $request->to_date]);
        }

        $entries = $query->get();

        return view('admin_panel.accounts.ledger', compact('account', 'entries'));
    }

    public function toggleStatus($id)
    {
        $account = Account::findOrFail($id);
        $account->status = ! $account->status;
        $account->save();

        return back()->with('success', 'Account status updated successfully!');
    }
}
