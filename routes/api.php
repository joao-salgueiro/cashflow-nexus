<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\CashFlow;
use App\Models\Forecast;
use App\Models\Anomaly;
use App\Models\Scenario;

// Health check
Route::get('/health', function () {
    return ['status' => 'ok', 'message' => 'CashFlow Nexus API is running!'];
});

// Cash flows
Route::get('/cash-flows', function () {
    return response()->json(CashFlow::all());
});

Route::get('/cash-flows/{id}', function ($id) {
    return response()->json(CashFlow::findOrFail($id));
});

Route::post('/cash-flows', function (Request $request) {
    $validated = $request->validate([
        'date' => 'required|date',
        'amount' => 'required|numeric',
        'type' => 'required|in:income,expense',
        'category' => 'nullable|string',
        'description' => 'nullable|string',
    ]);

    return response()->json(CashFlow::create($validated), 201);
});

// Forecasts
Route::get('/forecasts', function () {
    return response()->json(Forecast::all());
});

Route::post('/forecasts', function (Request $request) {
    $validated = $request->validate([
        'forecast_date' => 'required|date',
        'predicted_balance' => 'required|numeric',
        'confidence_score' => 'required|numeric|min:0|max:100',
        'method' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    return response()->json(Forecast::create($validated), 201);
});

// Anomalies
Route::get('/anomalies', function () {
    return response()->json(Anomaly::with('cashFlow')->get());
});

// Scenarios
Route::get('/scenarios', function () {
    return response()->json(Scenario::all());
});

Route::post('/scenarios', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'parameters' => 'required|array',
        'projected_balance' => 'required|numeric',
        'projected_period' => 'nullable|string',
    ]);

    return response()->json(Scenario::create($validated), 201);
});
