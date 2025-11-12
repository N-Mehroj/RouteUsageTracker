# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1-beta] - 2025-11-13

### 🛠️ **Bug Fixes & Production Ready**

#### ✅ **Fixed**
- **Production CDN Warning**: Replaced development Tailwind CDN with production-ready version
- **404 Error**: Fixed missing dashboard.js file by implementing standalone dashboard view
- **Automatic Route Registration**: Dashboard route now automatically available at `/route-usage-dashboard`
- **Inertia.js Compatibility**: Added fallback for applications without Inertia.js setup
- **Performance**: Optimized loading and reduced external dependencies

#### 🎨 **Enhanced**
- **Standalone Dashboard**: Complete self-contained dashboard with inline Vue.js
- **Better Error Handling**: Improved error messages and fallback mechanisms
- **Theme System**: Enhanced dark/light theme with better persistence
- **Loading States**: Added proper loading indicators and animations

#### 📦 **Technical Improvements**
- **Asset Management**: Eliminated need for external dashboard.js file
- **CDN Optimization**: Smart CDN loading with production/development detection
- **Route Automation**: Users no longer need to manually register dashboard routes

---

## [2.0.0-beta] - 2025-11-13

### 🎉 **Major Release - Dashboard & Inertia.js Support**

This is a major release introducing a complete Vue.js 3 dashboard with Inertia.js support, transforming the package from a simple tracking tool into a comprehensive route analytics platform.

### ✨ **Added**

#### Dashboard Features
- **Vue.js 3 Dashboard**: Complete reactive dashboard with modern UI
- **Inertia.js Integration**: Seamless SPA experience with server-side benefits
- **Interactive Charts**: Chart.js integration for beautiful data visualizations
- **Dark/Light Theme**: Automatic theme switching with system preference detection
- **Advanced Filtering**: Real-time filtering by route type, method, date range, and search
- **CSV Export**: Export filtered route data for external analysis
- **Responsive Design**: Mobile-first design with Tailwind CSS
- **Real-time Updates**: Live statistics and chart updates

#### New Commands
- `route-usage-tracker:setup` - One-command installation for complete dashboard setup
- `route-usage-tracker:publish-dashboard` - Publish Inertia.js Vue components
- Command options: `--skip-npm`, `--skip-migration`, `--force`

#### Enhanced Tracking
- **Route Type Detection**: Automatic categorization (web, api, admin, dashboard, auth, assets)
- **Enhanced API Endpoints**: RESTful API for dashboard data
- **Improved Performance**: Optimized queries and caching

#### Technical Improvements
- **Controller Architecture**: New DashboardController with 8 API endpoints
- **Component Structure**: Modular Vue.js components (Dashboard.vue, StatCard.vue)
- **Configuration Examples**: Complete setup examples for Vite, NPM, and Inertia.js

### 🔧 **Enhanced**
- **Migration**: Added `route_type` column for better categorization
- **Model**: Enhanced RouteUsage model with new helper methods
- **Middleware**: Improved route type detection and null handling
- **Service Provider**: Added route and view loading for dashboard
- **Documentation**: Comprehensive dashboard installation and usage guide

### 🛠️ **Fixed**
- **SQL Constraint**: Fixed "Column 'route_name' cannot be null" error
- **Null Route Handling**: Added fallback for unnamed routes using URI
- **Type Detection**: Improved automatic route type categorization logic

### 📦 **Dependencies**
- **Vue.js 3**: ^3.3.0
- **Inertia.js**: @inertiajs/vue3
- **Chart.js**: ^4.4.0
- **Heroicons**: @heroicons/vue ^2.0.0
- **VueUse**: @vueuse/core ^10.5.0

### 🚨 **Breaking Changes**
- **Minimum Requirements**: Laravel 9+ now required
- **Database**: New migration adds `route_type` column
- **Configuration**: New dashboard routes and API endpoints

### 📖 **Documentation**
- Complete Inertia.js setup guide
- One-command installation instructions
- Dashboard features and API documentation
- Configuration examples for Vite and NPM
- Troubleshooting and best practices

---

## [1.0.0] - 2025-11-12

### 🎉 **Initial Release**

#### ✨ **Added**
- **Automatic Route Tracking**: Global middleware for zero-configuration tracking
- **Statistics Collection**: Usage count, first/last access timestamps
- **Artisan Commands**: CLI interface for viewing route statistics
- **Filtering Options**: Filter by method, date range, and top routes
- **Facade Support**: Easy programmatic access to route data
- **Smart Route Ignoring**: Configurable ignored routes and patterns
- **MIT License**: Open source license for community use

#### 📊 **Core Features**
- Route usage counting
- First and last access tracking
- HTTP method tracking
- Route name and path storage
- Configurable ignored routes

#### 💻 **Commands**
- `php artisan route:usage` - View route statistics
- Options: `--top`, `--method`, `--from`, `--to`

#### 🛠️ **Technical**
- Laravel package auto-discovery
- Automatic migration loading
- Global middleware registration
- Eloquent model for route data