<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    // The primary source of truth for all Financial Reporting.
    
    protected $guarded = [];

    protected $casts = [
        'entry_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    // Polymorphic relation to Source (VoucherMaster, Sale, Purchase, etc.)
    public function source()
    {
        return $this->morphTo();
    }

    // Polymorphic relation to Party (Customer, Vendor, etc.)
    public function party()
    {
        return $this->morphTo();
    }

    /**
     * Resolve the Party or Source for General Ledger display.
     * When cash or general ledger entries do not have a direct party assigned,
     * this derives the counterparty/source (Customer, Vendor, Walk-in, Expense, Account)
     * from the linked voucher, sale, purchase, or opposing entry.
     */
    public function getResolvedPartyAttribute(): ?array
    {
        // 1. Direct party attached to this journal entry
        if ($this->party) {
            if ($this->party_type === Customer::class) {
                return [
                    'name' => $this->party->customer_name ?? 'Customer',
                    'type' => 'Customer',
                    'badge' => 'bg-primary',
                ];
            } elseif ($this->party_type === Vendor::class) {
                return [
                    'name' => $this->party->name ?? 'Vendor',
                    'type' => 'Vendor',
                    'badge' => 'bg-warning text-dark',
                ];
            } else {
                return [
                    'name' => $this->party->title ?? $this->party->name ?? 'Account',
                    'type' => 'Account',
                    'badge' => 'bg-info text-dark',
                ];
            }
        }

        $source = $this->source;

        // 2. VoucherMaster source
        if ($source instanceof VoucherMaster) {
            // 2a. Party directly attached to VoucherMaster
            if ($source->party) {
                if ($source->party_type === Customer::class) {
                    return [
                        'name' => $source->party->customer_name ?? 'Customer',
                        'type' => 'Customer',
                        'badge' => 'bg-primary',
                    ];
                } elseif ($source->party_type === Vendor::class) {
                    return [
                        'name' => $source->party->name ?? 'Vendor',
                        'type' => 'Vendor',
                        'badge' => 'bg-warning text-dark',
                    ];
                } elseif ($source->party_type === Account::class) {
                    return [
                        'name' => $source->party->title ?? 'Account',
                        'type' => 'Account',
                        'badge' => 'bg-info text-dark',
                    ];
                }
            }

            // 2b. Check if remarks/description references a Sale Invoice
            $textToCheck = ($source->remarks ?? '') . ' ' . ($this->description ?? '');
            if (preg_match('/#(INV-[A-Za-z0-9\-_]+)/i', $textToCheck, $m) || preg_match('/(?:Invoice|Sale)\s*#?\s*(INV-[A-Za-z0-9\-_]+)/i', $textToCheck, $m)) {
                $invNo = $m[1];
                $sale = Sale::where('invoice_no', $invNo)->with('customer_relation')->first();
                if ($sale) {
                    if ($sale->customer_relation) {
                        return [
                            'name' => $sale->customer_relation->customer_name,
                            'type' => 'Customer',
                            'badge' => 'bg-primary',
                        ];
                    } elseif (!empty($sale->walkin_name)) {
                        return [
                            'name' => $sale->walkin_name,
                            'type' => 'Walk-in Customer',
                            'badge' => 'bg-info text-dark',
                        ];
                    }
                }
            }

            // 2c. Check if remarks/description references a Purchase
            if (preg_match('/#(PUR-[A-Za-z0-9\-_]+)/i', $textToCheck, $m) || preg_match('/(?:Purchase|PUR)\s*#?\s*(PUR-[A-Za-z0-9\-_]+)/i', $textToCheck, $m)) {
                $purNo = $m[1];
                $pur = Purchase::where('invoice_no', $purNo)->first();
                if ($pur && $pur->vendor) {
                    return [
                        'name' => $pur->vendor->name,
                        'type' => 'Vendor',
                        'badge' => 'bg-warning text-dark',
                    ];
                }
            }

            // 2d. Check opposing line in VoucherMaster details (Contra / Opposing Account)
            $opposingDetail = $source->details()
                ->where('account_id', '!=', $this->account_id)
                ->with('account')
                ->first();
            if ($opposingDetail && $opposingDetail->account) {
                return [
                    'name' => $opposingDetail->account->title,
                    'type' => 'Account',
                    'badge' => 'bg-secondary',
                ];
            }
        }

        // 3. ExpenseVoucher source
        if ($source instanceof ExpenseVoucher) {
            if ($source->type === 'vendor') {
                $v = Vendor::find($source->party_id);
                if ($v) {
                    return ['name' => $v->name, 'type' => 'Vendor', 'badge' => 'bg-warning text-dark'];
                }
            } elseif ($source->type === 'customer') {
                $c = Customer::find($source->party_id);
                if ($c) {
                    return ['name' => $c->customer_name, 'type' => 'Customer', 'badge' => 'bg-primary'];
                }
            }

            $catIds = json_decode($source->row_account_id ?? '[]', true);
            if (is_array($catIds) && count($catIds) > 0) {
                $catNames = \Illuminate\Support\Facades\DB::table('expense_categories')
                    ->whereIn('id', $catIds)
                    ->pluck('name')
                    ->toArray();
                if (!empty($catNames)) {
                    return [
                        'name' => implode(', ', $catNames),
                        'type' => 'Expense',
                        'badge' => 'bg-danger',
                    ];
                }
            }

            return ['name' => 'General Expense', 'type' => 'Expense', 'badge' => 'bg-danger'];
        }

        // 4. ReceiptsVoucher source
        if ($source instanceof ReceiptsVoucher) {
            if ($source->type === 'customer' || empty($source->type)) {
                $c = Customer::find($source->party_id);
                if ($c) {
                    return ['name' => $c->customer_name, 'type' => 'Customer', 'badge' => 'bg-primary'];
                }
            } elseif ($source->type === 'vendor') {
                $v = Vendor::find($source->party_id);
                if ($v) {
                    return ['name' => $v->name, 'type' => 'Vendor', 'badge' => 'bg-warning text-dark'];
                }
            }
        }

        // 5. PaymentVoucher source
        if ($source instanceof PaymentVoucher) {
            if ($source->type === 'vendor' || empty($source->type)) {
                $v = Vendor::find($source->party_id ?? $source->vendor_id);
                if ($v) {
                    return ['name' => $v->name, 'type' => 'Vendor', 'badge' => 'bg-warning text-dark'];
                }
            } elseif ($source->type === 'customer') {
                $c = Customer::find($source->party_id ?? $source->vendor_id);
                if ($c) {
                    return ['name' => $c->customer_name, 'type' => 'Customer', 'badge' => 'bg-primary'];
                }
            }
        }

        // 6. Direct Sale model as source
        if ($source instanceof Sale) {
            if ($source->customer_relation) {
                return ['name' => $source->customer_relation->customer_name, 'type' => 'Customer', 'badge' => 'bg-primary'];
            } elseif (!empty($source->walkin_name)) {
                return ['name' => $source->walkin_name, 'type' => 'Walk-in Customer', 'badge' => 'bg-info text-dark'];
            }
        }

        // 7. Direct Purchase model as source
        if ($source instanceof Purchase) {
            if ($source->vendor) {
                return ['name' => $source->vendor->name, 'type' => 'Vendor', 'badge' => 'bg-warning text-dark'];
            }
        }

        // 8. Fallback check description for Invoice / Purchase
        if (preg_match('/#(INV-[A-Za-z0-9\-_]+)/i', $this->description ?? '', $m) || preg_match('/(?:Invoice|Sale)\s*#?\s*(INV-[A-Za-z0-9\-_]+)/i', $this->description ?? '', $m)) {
            $invNo = $m[1];
            $sale = Sale::where('invoice_no', $invNo)->with('customer_relation')->first();
            if ($sale) {
                if ($sale->customer_relation) {
                    return [
                        'name' => $sale->customer_relation->customer_name,
                        'type' => 'Customer',
                        'badge' => 'bg-primary',
                    ];
                } elseif (!empty($sale->walkin_name)) {
                    return [
                        'name' => $sale->walkin_name,
                        'type' => 'Walk-in Customer',
                        'badge' => 'bg-info text-dark',
                    ];
                }
            }
        }

        if (preg_match('/#(PUR-[A-Za-z0-9\-_]+)/i', $this->description ?? '', $m) || preg_match('/(?:Purchase|PUR)\s*#?\s*(PUR-[A-Za-z0-9\-_]+)/i', $this->description ?? '', $m)) {
            $purNo = $m[1];
            $pur = Purchase::where('invoice_no', $purNo)->first();
            if ($pur && $pur->vendor) {
                return [
                    'name' => $pur->vendor->name,
                    'type' => 'Vendor',
                    'badge' => 'bg-warning text-dark',
                ];
            }
        }

        return null;
    }
}
