<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anomaly extends Model
{
    protected $fillable = [
        'cash_flow_id',
        'anomaly_type',
        'severity',
        'expected_amount',
        'actual_amount',
        'description',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    // Relationship: An anomaly belongs to one cash flow
    public function cashFlow()
    {
        return $this->belongsTo(CashFlow::class);
    }
}