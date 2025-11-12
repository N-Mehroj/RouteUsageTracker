<?php

namespace NMehroj\RouteUsageTracker\Middleware;

use Closure;
use Illuminate\Http\Request;
use NMehroj\RouteUsageTracker\Models\RouteUsage;

class TrackRouteUsage
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if tracking is enabled
        if (!config('route-usage-tracker.enabled', true)) {
            return $response;
        }

        if ($route = $request->route()) {
            // Prefer named route when available, otherwise fall back to the route URI
            $routeName = $route->getName() ?: $route->uri();
            $routePath = $route->uri();
            $method = $request->method();
            $routeType = $this->determineRouteType($routePath, $routeName);

            // Skip if route or method should be ignored
            if ($this->shouldIgnore($routeName, $routePath, $method)) {
                return $response;
            }

            $now = now();

            // Find existing record or create new one
            $routeUsage = RouteUsage::where('route_name', $routeName)
                ->where('method', $method)
                ->first();

            if ($routeUsage) {
                // Update existing record
                $routeUsage->increment('usage_count');
                $routeUsage->update(['last_used_at' => $now]);
            } else {
                // Create new record
                RouteUsage::create([
                    'route_name' => $routeName,
                    'route_path' => $routePath,
                    'method' => $method,
                    'route_type' => $routeType,
                    'usage_count' => 1,
                    'first_used_at' => $now,
                    'last_used_at' => $now,
                ]);
            }
        }

        return $response;
    }

    /**
     * Check if the route should be ignored
     */
    private function shouldIgnore($routeName, $routePath, $method)
    {
        $ignoredRoutes = config('route-usage-tracker.ignored_routes', []);
        $ignoredMethods = config('route-usage-tracker.ignored_methods', []);

        // Check ignored methods
        if (in_array($method, $ignoredMethods)) {
            return true;
        }

        // Check ignored routes (support for wildcards)
        foreach ($ignoredRoutes as $pattern) {
            if ($routeName && fnmatch($pattern, $routeName)) {
                return true;
            }
            if (fnmatch($pattern, $routePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine the route type based on path and name
     */
    private function determineRouteType($routePath, $routeName)
    {
        // API routes
        if (str_starts_with($routePath, 'api/')) {
            return 'api';
        }

        // Admin routes
        if (
            str_starts_with($routePath, 'admin/') ||
            ($routeName && str_starts_with($routeName, 'admin.'))
        ) {
            return 'admin';
        }

        // Dashboard routes
        if (
            str_starts_with($routePath, 'dashboard/') ||
            ($routeName && str_starts_with($routeName, 'dashboard.'))
        ) {
            return 'dashboard';
        }

        // Authentication routes
        if (
            in_array($routePath, ['login', 'register', 'logout', 'password/reset']) ||
            ($routeName && in_array($routeName, ['login', 'register', 'logout', 'password.reset']))
        ) {
            return 'auth';
        }

        // Public/asset routes
        if (
            str_starts_with($routePath, 'storage/') ||
            str_starts_with($routePath, 'assets/') ||
            str_contains($routePath, '/css/') ||
            str_contains($routePath, '/js/') ||
            str_contains($routePath, '/images/')
        ) {
            return 'assets';
        }

        // Default to web
        return 'web';
    }
}
