<?php

namespace NMehroj\RouteUsageTracker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use NMehroj\RouteUsageTracker\Middleware\TrackRouteUsage;
use NMehroj\RouteUsageTracker\Commands\RouteUsageCommand;

class RouteUsageTrackerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load migrations automatically
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load routes for dashboard
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // Load views for dashboard
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'route-usage-tracker');

        // Register global middleware automatically
        if (!$this->app->runningInConsole()) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(TrackRouteUsage::class);
        }

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RouteUsageCommand::class,
                \NMehroj\RouteUsageTracker\Commands\PublishDashboardCommand::class,
                \NMehroj\RouteUsageTracker\Commands\SetupDashboardCommand::class,
            ]);

            // Publishing options
            $this->publishes([
                __DIR__ . '/../config/route-usage-tracker.php' => config_path('route-usage-tracker.php'),
            ], 'route-usage-tracker-config');

            // Publish dashboard assets
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/route-usage-tracker'),
                __DIR__ . '/../resources/js' => resource_path('js/vendor/route-usage-tracker'),
            ], 'route-usage-tracker-dashboard');
        }
    }

    public function register()
    {
        // Merge default configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/route-usage-tracker.php', 'route-usage-tracker');

        // Bind the service
        $this->app->bind('route-usage-tracker', function () {
            return new \NMehroj\RouteUsageTracker\Models\RouteUsage();
        });
    }
}
