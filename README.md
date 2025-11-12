# Laravel Route Usage Tracker

A Laravel package for automatically tracking and analyzing route usage statistics with zero configuration required.

## Description

This package allows you to track how routes are being used in your Laravel applications. It automatically records how many times each route has been accessed, when it was first and last used, and provides comprehensive statistics for performance analysis.

## Features

- ✅ **Zero Configuration** - Works immediately after installation
- 🚀 **Automatic Tracking** - Global middleware auto-registered
- 📊 **Rich Statistics** - Usage count, timestamps, and more
- 🎯 **Smart Filtering** - Configurable ignored routes and methods
- 💻 **Artisan Commands** - Powerful CLI for viewing statistics
- 🎨 **Facade Support** - Easy programmatic access
- 🔧 **Highly Configurable** - Customize behavior as needed

## Project Structure

```
├── src/
│   ├── Commands/
│   │   └── RouteUsageCommand.php
│   ├── Facades/
│   │   └── RouteUsageTracker.php
│   ├── Middleware/
│   │   └── TrackRouteUsage.php
│   ├── Models/
│   │   └── RouteUsage.php
│   └── RouteUsageTrackerServiceProvider.php
├── config/
│   └── route-usage-tracker.php
├── database/
│   └── migrations/
│       └── 2025_11_12_000000_create_route_usage_table.php
├── tests/
│   └── TrackRouteUsageTest.php
├── composer.json
├── phpunit.xml
├── README.md
├── LICENSE
├── .env.example
└── .gitignore
```

## Installation

### 1. Install via Composer

```bash
composer require nmehroj/route-usage-tracker
```

**Note:** The package will automatically register with Laravel via package discovery.

### 2. Automatic Setup (Ready to Use!)

🎉 **Zero Configuration Required!** The package works immediately after installation:

- ✅ Database migrations run automatically
- ✅ Global middleware is registered automatically
- ✅ Routes are tracked automatically on every request
- ✅ No manual setup needed

That's it! Your routes are now being tracked. Use `php artisan route:usage` to see statistics.

## Quick Start Example

```bash
# 1. Install the package
composer require nmehroj/route-usage-tracker

# 2. That's it! Visit some pages in your app, then check stats:
php artisan route:usage
```

## Usage

### Middleware Registration (Optional)

The package automatically registers a global middleware, but you can also manually control it by adding it to specific route groups in `app/Http/Kernel.php`:

```php
// For global tracking (already done automatically)
protected $middleware = [
    // ...
    \NMehroj\RouteUsageTracker\Middleware\TrackRouteUsage::class,
];

// Or for specific route groups only
protected $middlewareGroups = [
    'web' => [
        // ...
        \NMehroj\RouteUsageTracker\Middleware\TrackRouteUsage::class,
    ],
    
    'api' => [
        // ...
        \NMehroj\RouteUsageTracker\Middleware\TrackRouteUsage::class,
    ],
];
```

### Automatic Route Tracking

All routes are automatically tracked out of the box. For each route request, the following information is stored:

- Route name
- URL path
- HTTP method (GET, POST, PUT, DELETE, etc.)
- Usage count
- First and last usage timestamps

### Viewing Statistics

#### Via Artisan Command

```bash
# View all route statistics
php artisan route:usage

# View top 10 most used routes
php artisan route:usage --top=10

# View statistics for a specific date range
php artisan route:usage --from="2023-01-01" --to="2023-12-31"

# Filter by HTTP method
php artisan route:usage --method=POST

# Filter by route type (web, api, admin, dashboard, auth, assets)
php artisan route:usage --type=api

# Combine all parameters
php artisan route:usage --top=5 --method=GET --type=web --from="2024-01-01"
```

#### Via Code

##### Using the Model Directly

```php
use NMehroj\RouteUsageTracker\Models\RouteUsage;

// Get all route statistics
$stats = RouteUsage::all();

// Get top 10 most used routes
$topRoutes = RouteUsage::orderBy('usage_count', 'desc')->take(10)->get();

// Get specific route information
$routeStats = RouteUsage::where('route_name', 'home')->first();
```

##### Using the Facade (Recommended)

```php
use NMehroj\RouteUsageTracker\Facades\RouteUsageTracker;

// Get all route statistics
$stats = RouteUsageTracker::all();

// Get top 10 most used routes
$topRoutes = RouteUsageTracker::orderBy('usage_count', 'desc')->take(10)->get();

// Get specific route information
$routeStats = RouteUsageTracker::where('route_name', 'home')->first();

// Use helper methods
$topRoutes = RouteUsageTracker::getTopRoutes(10);
$getStats = RouteUsageTracker::getRoutesByMethod('GET');
$apiRoutes = RouteUsageTracker::getRoutesByType('api');
$summary = RouteUsageTracker::getStatsSummary();
```

## Database Structure

The `route_usage` table has the following columns:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| route_name | varchar(255) | Route name |
| route_path | varchar(500) | URL path |
| method | varchar(10) | HTTP method |
| route_type | varchar(50) | Route type (web, api, admin, dashboard, auth, assets) |
| usage_count | bigint | Usage count |
| first_used_at | timestamp | First usage timestamp |
| last_used_at | timestamp | Last usage timestamp |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

## Configuration

### Customizing Tracked Routes

The package automatically ignores common development and debugging routes. To customize which routes are tracked:

Publish the config file first:

```bash
php artisan vendor:publish --provider="NMehroj\RouteUsageTracker\RouteUsageTrackerServiceProvider" --tag="route-usage-tracker-config"
```

Then edit `config/route-usage-tracker.php` to add ignored routes:

```php
'ignored_routes' => [
    'telescope.*',
    'horizon.*',
    'debugbar.*',
    'admin.*',        // Ignore all admin routes
    'api/internal/*', // Ignore internal API routes
],
```

## Practical Examples

### 1. Dashboard Analytics

```php
use NMehroj\RouteUsageTracker\Models\RouteUsage;

class DashboardController extends Controller
{
    public function index()
    {
        $popularRoutes = RouteUsage::orderBy('usage_count', 'desc')
            ->take(5)
            ->get();
            
        return view('dashboard', compact('popularRoutes'));
    }
}
```

### 2. Weekly Usage Report

```php
use NMehroj\RouteUsageTracker\Models\RouteUsage;

// Get current week's statistics
$weeklyStats = RouteUsage::whereBetween('last_used_at', [
    now()->startOfWeek(),
    now()->endOfWeek()
])->orderBy('usage_count', 'desc')->get();

// Generate report
foreach ($weeklyStats as $stat) {
    echo "{$stat->route_name} ({$stat->method}): {$stat->usage_count} hits\n";
}
```

### 3. API Usage Analytics

```php
use NMehroj\RouteUsageTracker\Models\RouteUsage;

// Track API endpoint usage
$apiRoutes = RouteUsage::where('route_path', 'like', 'api/%')
    ->orderBy('usage_count', 'desc')
    ->get();

// Find underused endpoints
$underused = RouteUsage::where('usage_count', '<', 10)
    ->where('created_at', '>', now()->subMonth())
    ->get();
```

## Advanced Configuration

### Publishing Configuration

To customize the package behavior, publish the configuration file:

```bash
php artisan vendor:publish --provider="NMehroj\RouteUsageTracker\RouteUsageTrackerServiceProvider" --tag="route-usage-tracker-config"
```

Then edit `config/route-usage-tracker.php`:

```php
return [
    // Enable or disable route tracking
    'enabled' => env('ROUTE_USAGE_TRACKER_ENABLED', true),
    
    // Routes that will not be tracked (supports wildcards)
    'ignored_routes' => [
        'telescope.*',
        'horizon.*',
        'debugbar.*',
        '_debugbar/*',
        'livewire.*',
    ],
    
    // HTTP methods that will not be tracked
    'ignored_methods' => ['HEAD', 'OPTIONS'],
    
    // Database settings
    'database_connection' => env('ROUTE_USAGE_TRACKER_DB_CONNECTION', null),
    'table_name' => env('ROUTE_USAGE_TRACKER_TABLE', 'route_usage'),
    
    // Auto cleanup old data
    'auto_cleanup' => [
        'enabled' => env('ROUTE_USAGE_TRACKER_AUTO_CLEANUP', false),
        'days' => env('ROUTE_USAGE_TRACKER_CLEANUP_DAYS', 365),
    ],
];
```

## Environment Variables

You can add these variables to your `.env` file:

```env
# Control route tracking
ROUTE_USAGE_TRACKER_ENABLED=true

# Auto cleanup settings
ROUTE_USAGE_TRACKER_AUTO_CLEANUP=false
ROUTE_USAGE_TRACKER_CLEANUP_DAYS=365
```

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or 11.0

## Testing

To test the package:

```bash
# Install composer dependencies
composer install

# Run tests
composer test

# Or using PHPUnit directly
vendor/bin/phpunit

# Run tests with coverage
composer test-coverage
```

## Performance Considerations

- The package uses efficient database queries with proper indexing
- Middleware has minimal overhead (< 1ms per request)
- Automatic cleanup can be configured to prevent database bloat
- Uses Laravel's built-in caching where possible

## Troubleshooting

### Routes Not Being Tracked

1. Ensure the package is properly installed: `composer show nmehroj/route-usage-tracker`
2. Check if middleware is registered: `php artisan route:list --middleware`
3. Verify database migration ran: Check for `route_usage` table
4. Check configuration: `config('route-usage-tracker.enabled')`

### Performance Issues

1. Enable auto-cleanup to remove old data
2. Add database indexes if tracking high-volume routes
3. Consider excluding non-essential routes via configuration

## Contributing

We welcome contributions! To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- 📖 **Documentation**: This README
- 🐛 **Bug Reports**: [GitHub Issues](https://github.com/N-Mehroj/RouteUsageTracker/issues)
- 💡 **Feature Requests**: [GitHub Issues](https://github.com/N-Mehroj/RouteUsageTracker/issues)
- ❓ **Questions**: [GitHub Discussions](https://github.com/N-Mehroj/RouteUsageTracker/discussions)

## Author

**Nmehroj** - [GitHub](https://github.com/N-Mehroj)

---

Made with ❤️ for the Laravel community