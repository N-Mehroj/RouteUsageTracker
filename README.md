# Laravel Route Usage Tracker

[![Latest Version](https://img.shields.io/badge/version-2.0.0--beta-orange)](https://github.com/N-Mehroj/RouteUsageTracker)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-9%2B-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-Supported-purple.svg)](https://inertiajs.com)

A Laravel package for automatically tracking and analyzing route usage statistics with a beautiful Vue.js 3 dashboard and Inertia.js support.

> **🚨 Beta Release Notice**: Version 2.0.0-beta introduces major new features including Vue.js 3 dashboard and Inertia.js support. While stable for testing, please use with caution in production environments.

## Description

This package allows you to track how routes are being used in your Laravel applications. It automatically records how many times each route has been accessed, when it was first and last used, and provides comprehensive statistics for performance analysis.

## Features

### 🎯 **Core Tracking Features**
- ✅ **Zero Configuration** - Works immediately after installation
- 🚀 **Automatic Tracking** - Global middleware auto-registered
- 📊 **Rich Statistics** - Usage count, timestamps, route types and more
- 🎯 **Smart Filtering** - Configurable ignored routes and methods
- 💻 **Artisan Commands** - Powerful CLI for viewing statistics
- 🎨 **Facade Support** - Easy programmatic access

### 🎨 **Dashboard Features (Beta)**
- 📱 **Vue.js 3 Dashboard** - Modern, reactive user interface
- ⚡ **Inertia.js Support** - Seamless SPA experience
- 🌙 **Dark/Light Theme** - Automatic theme switching
- 📈 **Interactive Charts** - Beautiful Chart.js visualizations
- 🔍 **Advanced Filtering** - Real-time search and filters
- 💾 **CSV Export** - Export usage data for analysis
- 📊 **Real-time Updates** - Live statistics and charts
- 🎛️ **One Command Setup** - Install everything with single command

### 🔧 **Technical Features**
- 🏷️ **Route Type Detection** - Automatically categorizes routes (web, api, admin, etc.)
- � **Performance Optimized** - Minimal overhead on requests
- �🔧 **Highly Configurable** - Customize behavior as needed
- 🛡️ **Laravel 9+ Compatible** - Works with latest Laravel versions


## Installation

### 🔥 **Beta Version Installation**

To install the latest beta version with Vue.js 3 dashboard:

```bash
composer require nmehroj/route-usage-tracker:^2.0.0-beta
```

### 📦 **Stable Version Installation**

For the stable version (basic tracking only):

```bash
composer require nmehroj/route-usage-tracker:^1.0
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

## Dashboard with Inertia.js & Vue.js

The package includes a modern, reactive Vue.js 3 dashboard built with Inertia.js for visualizing route usage statistics, similar to Laravel Nightwatch.

### Inertia.js Dashboard Installation

#### 🚀 One-Command Setup (Recommended)

```bash
php artisan route-usage-tracker:setup
```

This single command will:
- ✅ Run database migrations
- ✅ Install all required NPM packages (Vue.js 3, Inertia.js, Chart.js, Heroicons, VueUse)
- ✅ Publish dashboard assets
- ✅ Create example configuration files (vite.config.js, app.js)
- ✅ Display next steps

**Available Options:**
```bash
# Skip NPM package installation
php artisan route-usage-tracker:setup --skip-npm

# Skip database migrations
php artisan route-usage-tracker:setup --skip-migration

# Force reinstall packages
php artisan route-usage-tracker:setup --force

# Skip both NPM and migrations
php artisan route-usage-tracker:setup --skip-npm --skip-migration
```

#### Manual Installation (Alternative)

1. **Ensure Inertia.js is set up in your Laravel application:**

Follow the [Inertia.js Laravel installation guide](https://inertiajs.com/server-side-setup) if not already installed.

2. **Publish dashboard assets:**
```bash
php artisan route-usage-tracker:publish-dashboard
```

3. **Install required dependencies:**
```bash
npm install vue@^3.3.0 @inertiajs/vue3 chart.js @heroicons/vue @vueuse/core
```

4. **Update your `resources/js/app.js` for Inertia.js:**
```js
import './bootstrap'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
    progress: {
        color: '#4F46E5',
    },
})
```

5. **Update your `vite.config.js`:**
```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
})
```

6. **Add dashboard route to your `routes/web.php`:**
```php
use NMehroj\RouteUsageTracker\Controllers\DashboardController;

Route::get('/route-usage-dashboard', [DashboardController::class, 'index'])->name('route-usage-tracker.dashboard');
```

7. **Build assets:**
```bash
npm run build
# or for development
npm run dev
```

### Dashboard Features

- 📊 **Real-time Statistics**: Live route usage metrics and charts
- 🎨 **Dark/Light Theme**: Automatic theme switching with system preference
- 📈 **Interactive Charts**: Beautiful Chart.js visualizations for usage trends
- 🔍 **Advanced Filtering**: Filter by route type, method, date range, and search
- 📱 **Responsive Design**: Works perfectly on desktop, tablet, and mobile
- 💾 **Export Functionality**: Export route data as CSV for further analysis
- ⚡ **Fast & Lightweight**: Built with Vue.js 3 Composition API for optimal performance

### Dashboard Usage

1. **Visit the dashboard:**
```
http://your-app.com/route-usage-dashboard
```

2. **Available dashboard endpoints:**
- `/route-usage-tracker/dashboard` - Main dashboard view
- `/route-usage-tracker/api/summary` - Statistics summary
- `/route-usage-tracker/api/routes` - Routes with filtering
- `/route-usage-tracker/api/daily-usage` - Daily usage charts
- `/route-usage-tracker/api/type-stats` - Route type statistics
- `/route-usage-tracker/api/top-routes` - Most used routes
- `/route-usage-tracker/api/recent-activity` - Recent route activity
- `/route-usage-tracker/api/export` - Export data as CSV

3. **Dashboard API parameters:**
```javascript
// Filter routes
fetch('/route-usage-tracker/api/routes?type=api&method=GET&search=user&limit=50')

// Get daily usage for last 7 days  
fetch('/route-usage-tracker/api/daily-usage?days=7')

// Get top 5 routes by type
fetch('/route-usage-tracker/api/top-routes?limit=5&type=web')

// Export filtered data
fetch('/route-usage-tracker/api/export?type=api&from=2024-01-01&to=2024-12-31')
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