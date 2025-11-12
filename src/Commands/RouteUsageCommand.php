<?php

namespace Mehroj\RouteUsageTracker\Commands;

use Illuminate\Console\Command;
use Mehroj\RouteUsageTracker\Models\RouteUsage;

class RouteUsageCommand extends Command
{
    protected $signature = 'route:usage';
    protected $description = 'Show route usage statistics';

    public function handle()
    {
        $this->table(
            ['Route name', 'Hits', 'Last used'],
            RouteUsage::all()->map(fn($r) => [
                $r->route_name,
                $r->hits,
                $r->last_used_at ?? 'Never used',
            ])
        );
    }
}
