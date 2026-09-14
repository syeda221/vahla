<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentLedger extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'commission_amount' => 'float',
        'payment_amount' => 'float',
        'balance' => 'float',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}