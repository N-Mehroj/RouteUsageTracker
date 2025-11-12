<?php

use Illuminate\Support\Facades\Route;
use NMehroj\RouteUsageTracker\Controllers\DashboardController;

Route::prefix('route-usage-tracker')->name('route-usage-tracker.')->group(function () {
    // Dashboard view
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
