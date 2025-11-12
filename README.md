# Laravel Route Usage Tracker

A Laravel package for tracking and analyzing route usage statistics in your application.

## Description

This package allows you to track how routes are being used in your Laravel applications. It records how many times each route has been accessed, when it was first and last used, and other useful statistics.

## Features

- Automatic route usage tracking
- Route statistics (hit count, first/last usage timestamps)
- Artisan command for reports
- Easy installation and configuration
- Zero configuration required - works out of the box

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

## Usage## Usage

### 1. Add Middleware (Optional)

The package automatically registers a global middleware, but you can also manually add it to specific routes in `app/Http/Kernel.php`:
### 3. Paketni avtomatik sozlash

#### Avtomatik o'rnatish (tavsiya etiladi)

```bash
php artisan route-usage-tracker:install
```

Bu komanda quyidagi amallarni avtomatik bajaradi:
- Config faylini publish qiladi
- Migration fayllarini publish qiladi  
- Migration'larni ishga tushiradi
- Middleware sozlash bo'yicha ko'rsatmalar beradi

#### Manual o'rnatish

Agar avtomatik o'rnatishni xohlamasangiz:

```bash
# Config faylini publish qilish
php artisan vendor:publish --provider="NMehroj\RouteUsageTracker\RouteUsageTrackerServiceProvider" --tag="route-usage-tracker-config"


# Migration'larni ishga tushirish
php artisan migrate
```

## Foydalanish

### 1. Middleware'ni ro'yxatdan o'tkazish

`app/Http/Kernel.php` fayliga middleware'ni qo'shing:

```php
protected $middleware = [
    // ...
    \NMehroj\RouteUsageTracker\Middleware\TrackRouteUsage::class,
];
```

Or for specific route groups:

```php
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

### 2. Route Tracking

Once the middleware is set up, all routes will be automatically tracked. For each route request, the following data is stored:

- Route name
- URL path
- HTTP method (GET, POST, PUT, DELETE, etc.)
- Usage count
- First and last usage timestamps

### 3. Viewing Statistics

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

# Combine all parameters
php artisan route:usage --top=5 --method=GET --from="2024-01-01"
```

#### Via Code

##### Using the Model

```php
use NMehroj\RouteUsageTracker\Models\RouteUsage;

// Get all route statistics
$stats = RouteUsage::all();

// Get top 10 most used routes
$topRoutes = RouteUsage::orderBy('usage_count', 'desc')->take(10)->get();

// Get specific route information
$routeStats = RouteUsage::where('route_name', 'home')->first();
```

##### Using the Facade (recommended)

```php
use NMehroj\RouteUsageTracker\Facades\RouteUsageTracker;

// Get all route statistics
$stats = RouteUsageTracker::all();

// Get top 10 most used routes
$topRoutes = RouteUsageTracker::orderBy('usage_count', 'desc')->take(10)->get();

// Get specific route information
$routeStats = RouteUsageTracker::where('route_name', 'home')->first();
```

## Database Structure

The `route_usage` table has the following columns:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| route_name | varchar(255) | Route name |
| route_path | varchar(500) | URL path |
| method | varchar(10) | HTTP method |
| usage_count | bigint | Usage count |
| first_used_at | timestamp | First usage timestamp |
| last_used_at | timestamp | Last usage timestamp |
| created_at | timestamp | Created timestamp |
| updated_at | timestamp | Updated timestamp |

## Configuration

### Ignoring Specific Routes

If you want to ignore certain routes from tracking, you can customize the configuration:

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

## Examples

### 1. Display Popular Pages in Dashboard

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

### 2. Generate Weekly Report

```php
use NMehroj\RouteUsageTracker\Models\RouteUsage;

$weeklyStats = RouteUsage::whereBetween('last_used_at', [
    now()->startOfWeek(),
    now()->endOfWeek()
])->get();

foreach ($weeklyStats as $stat) {
    echo "{$stat->route_name}: {$stat->usage_count} times\n";
}
```

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or 11.0

## License

Distributed under the MIT License.

## Contributing

To contribute to this project:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## Author

**Nmehroj** - [GitHub](https://github.com/N-Mehroj)

## Configuration Options

After installation, you can customize settings in `config/route-usage-tracker.php`:

```php
return [
    // Enable or disable route tracking
    'enabled' => env('ROUTE_USAGE_TRACKER_ENABLED', true),
    
    // Routes that will not be tracked
    'ignored_routes' => [
        'telescope.*',
        'horizon.*',
        'debugbar.*',
    ],
    
    // HTTP methods that will not be tracked
    'ignored_methods' => ['HEAD', 'OPTIONS'],
    
    // Auto cleanup old data
    'auto_cleanup' => [
        'enabled' => false,
        'days' => 365,
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

## Testing

To test the package:

```bash
# Install composer dependencies
composer install

# Run tests
composer test

# Or using PHPUnit directly
vendor/bin/phpunit
```

## Support

If you have questions or need help, please create an issue on GitHub.