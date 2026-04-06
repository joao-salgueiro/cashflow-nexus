<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = [
        'date',
        'amount',
        'type',
        'category',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationship: A cash flow can have many anomalies
    public function anomalies()
    {
        return $this->hasMany(Anomaly::class);
    }
}