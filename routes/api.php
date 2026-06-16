<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/* ── Public ── */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

/* ── Protected ── */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::apiResource('reports', ReportController::class);
    Route::get('/reports/{id}/pdf', [PdfController::class, 'generate']);

    Route::get('/settings',  [SettingController::class, 'show']);
    Route::post('/settings', [SettingController::class, 'update']);
});
