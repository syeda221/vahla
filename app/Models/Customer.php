<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'parent_id', 'customer_id', 'customer_name', 'customer_name_ur', 'cnic', 'filer_type', 'zone',
        'contact_person', 'mobile', 'email_address', 'contact_person_2', 'mobile_2',
        'email_address_2', 'opening_balance', 'balance_range', 'address', 'status',
        'customer_type', 'previous_balance', 'sales_officer_id',
        'payment_reminder_date', 'reminder_snoozed_at', 'reminder_day', 'source'
    ];

    /**
     * Parent customer relationship
     */
    public function parent()
    {
        return $this->belongsTo(Customer::class, 'parent_id');
    }

    /**
     * Sub-customers relationship
     */
    public function subCustomers()
    {
        return $this->hasMany(Customer::class, 'parent_id');
    }

    /**
     * Check if this customer is a sub-customer
     */
    public function isSubCustomer(): bool
    {
        return !empty($this->parent_id);
    }

    /**
     * Check if this customer has sub-customers
     */
    public function hasSubCustomers(): bool
    {
        return $this->subCustomers()->exists();
    }

    public function salesOfficer()
    {
        return $this->belongsTo(SalesOfficer::class, 'sales_officer_id');
    }

    /**
     * Polymorphic relationship to journal entries
     */
    public function journalEntries()
    {
        return $this->morphMany(JournalEntry::class, 'party');
    }

    /**
     * Get current balance from BalanceService
     */
    public function getPreviousBalanceAttribute()
    {
        // Use BalanceService to calculate the real-time balance
        // including opening balance and journal entries.
        try {
            $balanceService = app(\App\Services\BalanceService::class);
            return $balanceService->getCustomerBalance($this);
        } catch (\Exception $e) {
            // Fallback to column if service fails
            return $this->attributes['previous_balance'] ?? 0;
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
            if (!isset($model->is_synced)) {
                $model->is_synced = 0;
            }
        });
    }
}
