<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardApiController;

// La route qui affiche ta page HTML (Dashboard principal)
Route::get('/', function () {
    return view('dashboard');
});

// Les routes "API" appelées par ton code JavaScript (fetch)
Route::prefix('api')->group(function () {
    Route::get('/top-apps', [DashboardApiController::class, 'getTopApps']);
    Route::get('/evolution', [DashboardApiController::class, 'getEvolution']);
    Route::get('/comparison', [DashboardApiController::class, 'getComparison']);
    Route::get('/repartition', [DashboardApiController::class, 'getRepartition']); // Bonus 1
    Route::get('/alertes', [DashboardApiController::class, 'getAlertes']); // Bonus 2
});
