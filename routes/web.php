<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('productions', ProductionController::class)->only([
    'index', 'create', 'store', 'edit', 'update', 'destroy',
]);

Route::resource('claims', ClaimController::class)->only([
    'index', 'create', 'store', 'edit', 'update', 'destroy',
]);

Route::get('/summary', [SummaryController::class, 'index'])->name('summary');

Route::get('/reports/reinsurance.xlsx', [ReportController::class, 'download'])
    ->name('reports.reinsurance');