<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'voucher_type',
        'voucher_id',
        'voucher_master_id',
        'party_type',
        'party_id',
        'cheque_no',
        'cheque_date',
        'issue_date',
        'amount',
        'bank_name',
        'bank_account_id',
        'cleared_account_id',
        'status',
        'cleared_date',
        'alert_days_before',
        'low_balance_notified',
        'due_date_notified',
        'bounce_reason',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'issue_date' => 'date',
        'cleared_date' => 'date',
        'amount' => 'decimal:2',
        'low_balance_notified' => 'boolean',
        'due_date_notified' => 'boolean',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    public function clearedAccount()
    {
        return $this->belongsTo(Account::class, 'cleared_account_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'party_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'party_id');
    }

    public function voucherMaster()
    {
        return $this->belongsTo(VoucherMaster::class, 'voucher_master_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPartyNameAttribute()
    {
        if ($this->party_type === 'customer') {
            return $this->customer ? $this->customer->name : 'Customer #' . $this->party_id;
        }
        return $this->vendor ? $this->vendor->name : 'Vendor #' . $this->party_id;
    }
}
