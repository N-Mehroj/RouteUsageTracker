<?php

namespace Mehroj\RouteUsageTracker;

use Illuminate\Support\ServiceProvider;
use Mehroj\RouteUsageTracker\Middleware\TrackRouteUsage;
use Mehroj\RouteUsageTracker\Commands\RouteUsageCommand;

class RouteUsageTrackerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Migration’larni publish qilish
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Artisan komandani ro‘yxatdan o‘tkazish
        if ($this->app->runningInConsole()) {
            $this->commands([
                RouteUsageCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
