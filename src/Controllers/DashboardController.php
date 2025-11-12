<?php

namespace NMehroj\RouteUsageTracker\Controllers;

use NMehroj\RouteUsageTracker\Models\RouteUsage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController
{
    /**
     * Show the dashboard
     */
    public function index()
    {
        $summary = RouteUsage::getStatsSummary();
        $topRoutes = RouteUsage::orderBy('usage_count', 'desc')->limit(10)->get();
        $recentActivity = RouteUsage::orderBy('last_used_at', 'desc')->limit(10)->get();

        // Agar Inertia o'rnatilmagan bo'lsa, oddiy view qaytaramiz
        if (class_exists('\Inertia\Inertia')) {
            return \Inertia\Inertia::render('RouteUsageTracker/Dashboard', [
                'summary' => $summary,
                'topRoutes' => $topRoutes,
                'recentActivity' => $recentActivity,
                'apiEndpoints' => [
                    'summary' => '/route-usage-tracker/api/summary',
                    'routes' => '/route-usage-tracker/api/routes',
                    'dailyUsage' => '/route-usage-tracker/api/daily-usage',
                    'typeStats' => '/route-usage-tracker/api/type-stats',
                    'topRoutes' => '/route-usage-tracker/api/top-routes',
                    'recentActivity' => '/route-usage-tracker/api/recent-activity',
                    'periodStats' => '/route-usage-tracker/api/period-stats',
                    'export' => '/route-usage-tracker/api/export',
                ]
            ]);
        }

        // Fallback: Blade view qaytarish
        return view('route-usage-tracker::dashboard', compact('summary', 'topRoutes', 'recentActivity'));
    }

    /**
     * Get summary statistics
     */
    public function summary()
    {
        $summary = RouteUsage::getStatsSummary();
        return response()->json($summary);
    }

    /**
     * Get all routes with pagination and filtering
     */
    public function routes(\Illuminate\Http\Request $request)
    {
        $query = RouteUsage::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('route_type', $request->type);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('route_name', 'like', "%{$search}%")
                    ->orWhere('route_path', 'like', "%{$search}%");
            });
        }

        // Apply date range filters
        if ($request->filled('from')) {
            $query->where('last_used_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('last_used_at', '<=', $request->to);
        }

        $routes = $query->orderBy('usage_count', 'desc')
            ->limit($request->get('limit', 100))
            ->get();

        return response()->json($routes);
    }

    /**
     * Get daily usage statistics for charts
     */
    public function dailyUsage(\Illuminate\Http\Request $request)
    {
        $days = $request->get('days', 30);

        $dailyUsage = RouteUsage::select(
            DB::raw('DATE(last_used_at) as date'),
            DB::raw('SUM(usage_count) as count')
        )
            ->where('last_used_at', '>=', now()->subDays($days))
            ->groupBy(DB::raw('DATE(last_used_at)'))
            ->orderBy('date')
            ->get();

        return response()->json($dailyUsage);
    }

    /**
     * Get route type statistics
     */
    public function typeStats()
    {
        $typeStats = RouteUsage::select('route_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(usage_count) as total_usage'))
            ->groupBy('route_type')
            ->orderBy('total_usage', 'desc')
            ->get();

        return response()->json($typeStats);
    }

    /**
     * Get top routes by usage
     */
    public function topRoutes(\Illuminate\Http\Request $request)
    {
        $limit = $request->get('limit', 10);
        $type = $request->get('type');

        $query = RouteUsage::query();

        if ($type) {
            $query->where('route_type', $type);
        }

        $routes = $query->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($routes);
    }

    /**
     * Get recent activity
     */
    public function recentActivity(\Illuminate\Http\Request $request)
    {
        $limit = $request->get('limit', 20);

        $recent = RouteUsage::orderBy('last_used_at', 'desc')
            ->limit($limit)
            ->get(['route_name', 'route_path', 'method', 'route_type', 'last_used_at', 'usage_count']);

        return response()->json($recent);
    }

    /**
     * Get usage statistics for a specific time period
     */
    public function periodStats(\Illuminate\Http\Request $request)
    {
        $period = $request->get('period', 'today'); // today, week, month, year

        $startDate = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfDay()
        };

        $stats = RouteUsage::where('last_used_at', '>=', $startDate)
            ->selectRaw('
                COUNT(*) as total_routes,
                SUM(usage_count) as total_requests,
                AVG(usage_count) as avg_requests,
                route_type,
                COUNT(*) as routes_by_type
            ')
            ->groupBy('route_type')
            ->get();

        $summary = [
            'period' => $period,
            'start_date' => $startDate,
            'total_routes' => $stats->sum('total_routes'),
            'total_requests' => $stats->sum('total_requests'),
            'avg_requests' => $stats->avg('avg_requests'),
            'by_type' => $stats
        ];

        return response()->json($summary);
    }

    /**
     * Export usage data as CSV
     */
    public function export(\Illuminate\Http\Request $request)
    {
        $query = RouteUsage::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('route_type', $request->type);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('from')) {
            $query->where('last_used_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('last_used_at', '<=', $request->to);
        }

        $routes = $query->orderBy('usage_count', 'desc')->get();

        $filename = 'route-usage-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($routes) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Route Name', 'Route Path', 'Method', 'Type', 'Usage Count', 'First Used', 'Last Used']);

            // Add data rows
            foreach ($routes as $route) {
                fputcsv($file, [
                    $route->route_name,
                    $route->route_path,
                    $route->method,
                    $route->route_type,
                    $route->usage_count,
                    $route->first_used_at,
                    $route->last_used_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
