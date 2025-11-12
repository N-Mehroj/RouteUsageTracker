<?php

namespace Mehroj\RouteUsageTracker\Middleware;

use Closure;
use Mehroj\RouteUsageTracker\Models\RouteUsage;

class TrackRouteUsage
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($route = $request->route()) {
            $name = $route->getName() ?? $route->uri();
            RouteUsage::updateOrCreate(
                ['route_name' => $name],
                ['hits' => \DB::raw('hits + 1'), 'last_used_at' => now()]
            );
        }

        return $response;
    }
}
