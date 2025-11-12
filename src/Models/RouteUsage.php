<?php

namespace Mehroj\RouteUsageTracker\Models;

use Illuminate\Database\Eloquent\Model;

class RouteUsage extends Model
{
    protected $fillable = ['route_name', 'hits', 'last_used_at'];
    public $timestamps = false;
}
