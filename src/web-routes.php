<?php

use Illuminate\Support\Facades\Route;
use NMehroj\RouteUsageTracker\Controllers\DashboardController;

// Dashboard web route - avtomatik qo'shiladi
Route::middleware(['web'])->group(function () {
    Route::get('/route-usage-dashboard', [DashboardController::class, 'index'])
        ->name('route-usage-tracker.dashboard');
});

// API routes - dashboard uchun
Route::prefix('route-usage-tracker')->name('route-usage-tracker.')->group(function () {
    // API endpoints for dashboard data
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('summary', [DashboardController::class, 'summary'])->name('summary');
        Route::get('routes', [DashboardController::class, 'routes'])->name('routes');
        Route::get('daily-usage', [DashboardController::class, 'dailyUsage'])->name('daily-usage');
        Route::get('type-stats', [DashboardController::class, 'typeStats'])->name('type-stats');
        Route::get('top-routes', [DashboardController::class, 'topRoutes'])->name('top-routes');
        Route::get('recent-activity', [DashboardController::class, 'recentActivity'])->name('recent-activity');
        Route::get('period-stats', [DashboardController::class, 'periodStats'])->name('period-stats');
        Route::get('export', [DashboardController::class, 'export'])->name('export');
    });
});