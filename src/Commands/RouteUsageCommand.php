<?php

namespace NMehroj\RouteUsageTracker\Commands;

use Illuminate\Console\Command;
use NMehroj\RouteUsageTracker\Models\RouteUsage;
use Illuminate\Support\Carbon;

class RouteUsageCommand extends Command
{
    protected $signature = 'route:usage 
                            {--top= : Show top N routes by usage count}
                            {--from= : Start date for filtering (Y-m-d format)}
                            {--to= : End date for filtering (Y-m-d format)}
                            {--method= : Filter by HTTP method}';

    protected $description = 'Display route usage statistics with filtering options';

    public function handle()
    {
        $query = RouteUsage::query();

        // Apply filters
        $this->applyFilters($query);

        // Apply top limit if specified
        if ($this->option('top')) {
            $query->orderBy('usage_count', 'desc')->limit((int) $this->option('top'));
        } else {
            $query->orderBy('usage_count', 'desc');
        }

        $routes = $query->get();

        if ($routes->isEmpty()) {
            $this->info('No routes found matching the criteria.');
            return;
        }

        $this->displayResults($routes);
        $this->displaySummary($routes);
    }

    private function applyFilters($query)
    {
        // Filter by date range
        if ($this->option('from')) {
            $fromDate = now()->parse($this->option('from'))->startOfDay();
            $query->where('last_used_at', '>=', $fromDate);
        }

        if ($this->option('to')) {
            $toDate = now()->parse($this->option('to'))->endOfDay();
            $query->where('last_used_at', '<=', $toDate);
        }

        // Filter by HTTP method
        if ($this->option('method')) {
            $query->where('method', strtoupper($this->option('method')));
        }
    }

    private function displayResults($routes)
    {
        $headers = ['Route Name', 'Path', 'Method', 'Usage Count', 'First Used', 'Last Used'];

        $rows = $routes->map(function ($route) {
            return [
                $route->route_name ?: 'N/A',
                $route->route_path,
                $route->method,
                number_format($route->usage_count),
                $route->first_used_at ? $route->first_used_at->format('Y-m-d H:i:s') : 'Never',
                $route->last_used_at ? $route->last_used_at->format('Y-m-d H:i:s') : 'Never',
            ];
        });

        $this->table($headers, $rows);
    }

    private function displaySummary($routes)
    {
        $totalRoutes = $routes->count();
        $totalUsage = $routes->sum('usage_count');
        $averageUsage = $totalRoutes > 0 ? round($totalUsage / $totalRoutes, 2) : 0;

        $this->info("\n" . str_repeat('=', 50));
        $this->info("Summary:");
        $this->info("Total Routes: " . number_format($totalRoutes));
        $this->info("Total Usage: " . number_format($totalUsage));
        $this->info("Average Usage per Route: " . number_format($averageUsage));

        if ($routes->isNotEmpty()) {
            $mostUsed = $routes->first();
            $this->info("Most Used Route: {$mostUsed->route_name} ({$mostUsed->usage_count} times)");
        }
    }
}
