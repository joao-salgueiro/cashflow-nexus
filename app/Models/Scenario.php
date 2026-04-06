<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parameters',
        'projected_balance',
        'projected_period',
    ];

    protected $casts = [
        'parameters' => 'array',
        'projected_balance' => 'decimal:2',
    ];
}