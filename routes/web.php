<?php

use Illuminate\Support\Facades\Route;

// Lorsque l'utilisateur va sur http://localhost:8000
Route::get('/', function () {
    return view('dashboard');
});
