<?php

use App\Http\Controllers\PersonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/process-csv', [PersonController::class, 'processCsv']);
Route::get('/persons', [PersonController::class, 'index']);
