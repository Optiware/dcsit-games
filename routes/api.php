<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/top-apps', [DashboardController::class, 'getTopApps']);
Route::get('/evolution', [DashboardController::class, 'getEvolution']);
Route::get('/comparison', [DashboardController::class, 'getComparison']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
