<?php

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::post('/process-csv', [PersonController::class, 'processCsv']);
Route::get('/persons', [PersonController::class, 'index']);
