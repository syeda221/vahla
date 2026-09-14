<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
    ];

    public function ledgers()
    {
        return $this->hasMany(AgentLedger::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Total commission earned (payable) for this agent.
     */
    public function getTotalCommissionAttribute()
    {
        return (float) $this->ledgers()->sum('commission_amount');
    }

    /**
     * Total commission paid to this agent.
     */
    public function getPaidCommissionAttribute()
    {
        return (float) $this->ledgers()->sum('payment_amount');
    }

    /**
     * Outstanding / unpaid commission balance.
     */
    public function getOutstandingCommissionAttribute()
    {
        return max(0, $this->total_commission - $this->paid_commission);
    }

    public function getLastBalanceAttribute()
    {
        $last = $this->ledgers()->latest('id')->first();

        return $last ? (float) $last->balance : 0;
    }
}