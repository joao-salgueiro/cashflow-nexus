<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{
    protected $fillable = [
        'forecast_date',
        'predicted_balance',
        'confidence_score',
        'method',
        'notes',
    ];

    protected $casts = [
        'forecast_date' => 'date',
        'predicted_balance' => 'decimal:2',
        'confidence_score' => 'decimal:2',
    ];
}