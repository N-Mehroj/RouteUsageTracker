<?php

namespace NMehroj\RouteUsageTracker\Models;

use Illuminate\Database\Eloquent\Model;

class RouteUsage extends Model
{
    protected $table = 'route_usage';

    protected $fillable = [
        'route_name',
        'route_path',
        'method',
        'usage_count',
        'first_used_at',
        'last_used_at'
    ];

    protected $casts = [
        'first_used_at' => 'datetime',
        'last_used_at' => 'datetime',
        'usage_count' => 'integer'
    ];

    /**
     * Get top routes by usage count
     */
    public static function getTopRoutes($limit = 10)
    {
        return static::orderBy('usage_count', 'desc')->limit($limit)->get();
    }

    /**
     * Get routes by HTTP method
     */
    public static function getRoutesByMethod($method)
    {
        return static::where('method', strtoupper($method))->get();
    }

    /**
     * Get routes within date range
     */
    public static function getRoutesByDateRange($from, $to)
    {
        return static::whereBetween('last_used_at', [$from, $to])->get();
    }

    /**
     * Get usage statistics summary
     */
    public static function getStatsSummary()
    {
        $total = static::sum('usage_count');
        $routes = static::count();
        $average = $routes > 0 ? round($total / $routes, 2) : 0;

        return [
            'total_routes' => $routes,
            'total_usage' => $total,
            'average_usage' => $average,
            'most_used' => static::orderBy('usage_count', 'desc')->first()
        ];
    }
}
