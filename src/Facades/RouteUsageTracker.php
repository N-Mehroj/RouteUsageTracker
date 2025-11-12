<?php

namespace NMehroj\RouteUsageTracker\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Collection all()
 * @method static \NMehroj\RouteUsageTracker\Models\RouteUsage|null find(int $id)
 * @method static \Illuminate\Database\Eloquent\Builder where(string $column, mixed $operator, mixed $value = null)
 * @method static \Illuminate\Database\Eloquent\Builder orderBy(string $column, string $direction = 'asc')
 * @method static int count()
 * @method static \Illuminate\Database\Eloquent\Collection getTopRoutes(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Collection getRoutesByMethod(string $method)
 * @method static \Illuminate\Database\Eloquent\Collection getRoutesByDateRange(\Carbon\Carbon $from, \Carbon\Carbon $to)
 * @method static array getStatsSummary()
 *
 * @see \NMehroj\RouteUsageTracker\Models\RouteUsage
 */
class RouteUsageTracker extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'route-usage-tracker';
    }
}
