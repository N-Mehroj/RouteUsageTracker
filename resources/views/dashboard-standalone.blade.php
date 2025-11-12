<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Usage Tracker Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b',
                    }
                }
            }
        }
    </script>
    
    <!-- Vue.js 3 -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global{{ app()->environment('production') ? '.prod' : '' }}.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [v-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .loading-spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div id="app" v-cloak class="h-full">
        <div v-if="isLoading" class="flex items-center justify-center h-screen">
            <div class="text-center">
                <div class="loading-spinner mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-300">Loading Dashboard...</p>
            </div>
        </div>
        
        <div v-else class="fade-in min-h-screen">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center">
                            <i class="fas fa-route text-blue-500 text-2xl mr-3"></i>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                Route Usage Tracker
                            </h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Theme Toggle -->
                            <button
                                @click="toggleTheme"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                title="Toggle theme"
                            >
                                <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'" class="text-gray-600 dark:text-gray-300"></i>
                            </button>
                            
                            <!-- Refresh Button -->
                            <button
                                @click="refreshData"
                                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                title="Refresh data"
                            >
                                <i class="fas fa-refresh text-gray-600 dark:text-gray-300" :class="{ 'fa-spin': isRefreshing }"></i>
                            </button>
                            
                            <!-- Export Button -->
                            <button
                                @click="exportData"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                <i class="fas fa-download mr-2"></i>
                                Export CSV
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Error Message -->
                <div v-if="error" class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ error }}
                </div>

                <!-- Stats Cards -->
                <div class="px-4 py-6 sm:px-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Routes -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-blue-500 p-3 rounded-md">
                                            <i class="fas fa-route text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                                Total Routes
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ stats.totalRoutes || 'Loading...' }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Requests -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-green-500 p-3 rounded-md">
                                            <i class="fas fa-chart-line text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                                Total Requests
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ formatNumber(stats.totalRequests) || 'Loading...' }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Most Used Route -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-red-500 p-3 rounded-md">
                                            <i class="fas fa-fire text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                                Most Used Route
                                            </dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ stats.mostUsedRoute || 'Loading...' }}
                                            </dd>
                                            <dd class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ formatNumber(stats.mostUsedCount) }} requests
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Today -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="bg-purple-500 p-3 rounded-md">
                                            <i class="fas fa-clock text-white text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                                Active Today
                                            </dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ stats.activeToday || 'Loading...' }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Routes Table -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Route Statistics</h3>
                            <div class="flex space-x-2">
                                <select v-model="filters.type" @change="applyFilters" class="text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">All Types</option>
                                    <option value="web">Web</option>
                                    <option value="api">API</option>
                                    <option value="admin">Admin</option>
                                </select>
                                <input 
                                    v-model="filters.search" 
                                    @input="applyFilters"
                                    type="text" 
                                    placeholder="Search routes..."
                                    class="text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500"
                                >
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Route
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Method
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Usage Count
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Last Used
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-if="filteredRoutes.length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-inbox text-4xl mb-4 block"></i>
                                            No routes found
                                        </td>
                                    </tr>
                                    <tr v-for="route in filteredRoutes" :key="route.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ route.route_name || 'Unnamed Route' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ route.route_path }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getMethodClass(route.method)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ route.method }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ formatNumber(route.usage_count) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(route.last_used_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const { createApp, ref, computed, onMounted, watch } = Vue;

        createApp({
            setup() {
                // Reactive data
                const isLoading = ref(true);
                const isRefreshing = ref(false);
                const isDark = ref((() => {
                    const stored = localStorage.getItem('route-tracker-theme');
                    if (stored) return stored === 'dark';
                    return window.matchMedia('(prefers-color-scheme: dark)').matches;
                })());
                const error = ref('');
                const routes = ref([]);
                const stats = ref({
                    totalRoutes: 0,
                    totalRequests: 0,
                    mostUsedRoute: '',
                    mostUsedCount: 0,
                    activeToday: 0
                });

                // Filters
                const filters = ref({
                    type: '',
                    search: ''
                });

                // Computed
                const filteredRoutes = computed(() => {
                    let filtered = routes.value;
                    
                    if (filters.value.type) {
                        filtered = filtered.filter(route => route.route_type === filters.value.type);
                    }
                    
                    if (filters.value.search) {
                        const search = filters.value.search.toLowerCase();
                        filtered = filtered.filter(route => 
                            (route.route_name && route.route_name.toLowerCase().includes(search)) ||
                            (route.route_path && route.route_path.toLowerCase().includes(search))
                        );
                    }
                    
                    return filtered;
                });

                // Methods
                const toggleTheme = () => {
                    isDark.value = !isDark.value;
                    localStorage.setItem('route-tracker-theme', isDark.value ? 'dark' : 'light');
                    document.documentElement.classList.toggle('dark', isDark.value);
                    
                    // Dispatch custom event for theme change
                    window.dispatchEvent(new CustomEvent('themeChanged', { 
                        detail: { theme: isDark.value ? 'dark' : 'light' } 
                    }));
                };

                const loadData = async () => {
                    try {
                        error.value = '';
                        
                        // Parallel loading for better performance
                        const [summaryResponse, routesResponse] = await Promise.all([
                            fetch('/route-usage-tracker/api/summary'),
                            fetch('/route-usage-tracker/api/routes?limit=50')
                        ]);
                        
                        if (!summaryResponse.ok) throw new Error('Failed to load summary data');
                        if (!routesResponse.ok) throw new Error('Failed to load routes data');
                        
                        const [summaryData, routesData] = await Promise.all([
                            summaryResponse.json(),
                            routesResponse.json()
                        ]);
                        
                        stats.value = {
                            totalRoutes: summaryData.total_routes || 0,
                            totalRequests: summaryData.total_requests || 0,
                            mostUsedRoute: summaryData.most_used_route?.route_name || 'N/A',
                            mostUsedCount: summaryData.most_used_route?.usage_count || 0,
                            activeToday: summaryData.today_active_routes || 0
                        };

                        routes.value = routesData;

                    } catch (err) {
                        console.error('Error loading data:', err);
                        error.value = `Failed to load dashboard data: ${err.message}. Please check if the API endpoints are accessible.`;
                    }
                };

                const refreshData = async () => {
                    isRefreshing.value = true;
                    await loadData();
                    isRefreshing.value = false;
                };

                const exportData = async () => {
                    try {
                        const response = await fetch('/route-usage-tracker/api/export');
                        if (!response.ok) throw new Error('Export failed');
                        
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `route-usage-${new Date().toISOString().split('T')[0]}.csv`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    } catch (err) {
                        console.error('Export failed:', err);
                        error.value = 'Export failed. Please try again.';
                    }
                };

                const applyFilters = () => {
                    // Filters are applied via computed property
                };

                const formatNumber = (num) => {
                    if (!num) return '0';
                    return new Intl.NumberFormat().format(num);
                };

                const formatDate = (dateString) => {
                    if (!dateString) return 'Never';
                    return new Date(dateString).toLocaleString();
                };

                const getMethodClass = (method) => {
                    const classes = {
                        'GET': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                        'POST': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                        'PUT': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                        'PATCH': 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100',
                        'DELETE': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                    };
                    return classes[method] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100';
                };

                // Lifecycle
                onMounted(async () => {
                    document.documentElement.classList.toggle('dark', isDark.value);
                    await loadData();
                    isLoading.value = false;
                });

                // Watch theme changes
                watch(isDark, (newValue) => {
                    document.documentElement.classList.toggle('dark', newValue);
                });

                return {
                    isLoading,
                    isRefreshing,
                    isDark,
                    error,
                    routes,
                    stats,
                    filters,
                    filteredRoutes,
                    toggleTheme,
                    refreshData,
                    exportData,
                    applyFilters,
                    formatNumber,
                    formatDate,
                    getMethodClass
                };
            }
        }).mount('#app');
    </script>
</body>
</html>