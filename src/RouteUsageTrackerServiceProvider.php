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

        // Register global middleware automatically
        if (!$this->app->runningInConsole()) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(TrackRouteUsage::class);
        }

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RouteUsageCommand::class,
            ]);

            // Optional: Allow publishing config for customization
            $this->publishes([
                __DIR__ . '/../config/route-usage-tracker.php' => config_path('route-usage-tracker.php'),
            ], 'route-usage-tracker-config');
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
