const { createApp } = Vue;

// Main Dashboard Component
const Dashboard = {
    template: `
        <div class="min-h-screen transition-colors duration-200" :class="{ 'dark': isDarkMode }">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-route mr-2 text-blue-600"></i>
                                Route Usage Tracker
                            </h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button @click="toggleDarkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i :class="isDarkMode ? 'fas fa-sun' : 'fas fa-moon'" class="text-gray-600 dark:text-gray-300"></i>
                            </button>
                            <button @click="refreshData" class="btn-primary" :disabled="loading">
                                <i class="fas fa-sync-alt mr-2" :class="{ 'animate-spin': loading }"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Loading State -->
                <div v-if="loading && !data.summary" class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>

                <!-- Dashboard Content -->
                <div v-else>
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <stat-card 
                            title="Total Routes" 
                            :value="data.summary?.total_routes || 0"
                            icon="fas fa-route"
                            color="blue"
                        ></stat-card>
                        <stat-card 
                            title="Total Requests" 
                            :value="data.summary?.total_usage || 0"
                            icon="fas fa-chart-line"
                            color="green"
                        ></stat-card>
                        <stat-card 
                            title="Average per Route" 
                            :value="data.summary?.average_usage || 0"
                            icon="fas fa-calculator"
                            color="purple" 
                        ></stat-card>
                        <stat-card 
                            title="Most Active Today" 
                            :value="data.todayCount || 0"
                            icon="fas fa-fire"
                            color="red"
                        ></stat-card>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Usage Over Time Chart -->
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                                <i class="fas fa-chart-area mr-2"></i>
                                Usage Over Time
                            </h3>
                            <canvas ref="usageChart" width="400" height="200"></canvas>
                        </div>

                        <!-- Route Types Chart -->
                        <div class="card">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">
                                <i class="fas fa-pie-chart mr-2"></i>
                                Route Types Distribution
                            </h3>
                            <canvas ref="typeChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-6">
                        <div class="flex flex-wrap gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Type</label>
                                <select v-model="filters.type" @change="applyFilters" 
                                        class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                                    <option value="">All Types</option>
                                    <option value="web">Web</option>
                                    <option value="api">API</option>
                                    <option value="admin">Admin</option>
                                    <option value="dashboard">Dashboard</option>
                                    <option value="auth">Auth</option>
                                    <option value="assets">Assets</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Method</label>
                                <select v-model="filters.method" @change="applyFilters"
                                        class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                                    <option value="">All Methods</option>
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                    <option value="PATCH">PATCH</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Period</label>
                                <select v-model="filters.period" @change="applyFilters"
                                        class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                                    <option value="all">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Routes Table -->
                    <div class="card">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                <i class="fas fa-table mr-2"></i>
                                Route Details
                            </h3>
                            <div class="flex items-center space-x-2">
                                <input v-model="searchQuery" @input="applyFilters" type="text" placeholder="Search routes..."
                                       class="border rounded-lg px-3 py-2 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600">
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Route
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Method
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Usage Count
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Last Used
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="route in filteredRoutes" :key="route.id" 
                                        class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ route.route_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ route.route_path }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                  :class="getMethodColor(route.method)">
                                                {{ route.method }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                  :class="getTypeColor(route.route_type)">
                                                {{ route.route_type }}
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
    `,
    data() {
        return {
            loading: false,
            isDarkMode: localStorage.getItem('darkMode') === 'true',
            data: {
                summary: null,
                routes: [],
                dailyUsage: [],
                todayCount: 0
            },
            filters: {
                type: '',
                method: '',
                period: 'all'
            },
            searchQuery: '',
            filteredRoutes: [],
            usageChart: null,
            typeChart: null
        }
    },
    async mounted() {
        await this.loadData();
        this.initCharts();
        this.applyFilters();

        // Auto refresh every 30 seconds
        setInterval(() => {
            this.refreshData();
        }, 30000);
    },
    methods: {
        async loadData() {
            this.loading = true;
            try {
                const [summaryRes, routesRes, dailyRes] = await Promise.all([
                    fetch('/api/route-usage-tracker/summary'),
                    fetch('/api/route-usage-tracker/routes'),
                    fetch('/api/route-usage-tracker/daily-usage')
                ]);

                this.data.summary = await summaryRes.json();
                this.data.routes = await routesRes.json();
                this.data.dailyUsage = await dailyRes.json();
                this.data.todayCount = this.data.dailyUsage[this.data.dailyUsage.length - 1]?.count || 0;

                this.updateCharts();
            } catch (error) {
                console.error('Failed to load data:', error);
            } finally {
                this.loading = false;
            }
        },

        async refreshData() {
            await this.loadData();
            this.applyFilters();
        },

        toggleDarkMode() {
            this.isDarkMode = !this.isDarkMode;
            localStorage.setItem('darkMode', this.isDarkMode);
            document.documentElement.classList.toggle('dark', this.isDarkMode);
        },

        applyFilters() {
            let filtered = [...this.data.routes];

            if (this.filters.type) {
                filtered = filtered.filter(route => route.route_type === this.filters.type);
            }

            if (this.filters.method) {
                filtered = filtered.filter(route => route.method === this.filters.method);
            }

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(route =>
                    route.route_name.toLowerCase().includes(query) ||
                    route.route_path.toLowerCase().includes(query)
                );
            }

            // Apply period filter
            if (this.filters.period !== 'all') {
                const now = new Date();
                let cutoff;

                switch (this.filters.period) {
                    case 'today':
                        cutoff = new Date(now.setHours(0, 0, 0, 0));
                        break;
                    case 'week':
                        cutoff = new Date(now.setDate(now.getDate() - 7));
                        break;
                    case 'month':
                        cutoff = new Date(now.setMonth(now.getMonth() - 1));
                        break;
                }

                if (cutoff) {
                    filtered = filtered.filter(route =>
                        new Date(route.last_used_at) >= cutoff
                    );
                }
            }

            this.filteredRoutes = filtered.sort((a, b) => b.usage_count - a.usage_count);
        },

        initCharts() {
            this.initUsageChart();
            this.initTypeChart();
        },

        initUsageChart() {
            const ctx = this.$refs.usageChart.getContext('2d');
            this.usageChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Daily Usage',
                        data: [],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: this.isDarkMode ? 'rgba(75, 85, 99, 0.2)' : 'rgba(229, 231, 235, 0.2)'
                            }
                        },
                        x: {
                            grid: {
                                color: this.isDarkMode ? 'rgba(75, 85, 99, 0.2)' : 'rgba(229, 231, 235, 0.2)'
                            }
                        }
                    }
                }
            });
        },

        initTypeChart() {
            const ctx = this.$refs.typeChart.getContext('2d');
            this.typeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b',
                            '#ef4444', '#8b5cf6', '#06b6d4'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        updateCharts() {
            // Update usage chart
            if (this.usageChart && this.data.dailyUsage) {
                this.usageChart.data.labels = this.data.dailyUsage.map(d => d.date);
                this.usageChart.data.datasets[0].data = this.data.dailyUsage.map(d => d.count);
                this.usageChart.update();
            }

            // Update type chart
            if (this.typeChart && this.data.summary?.by_type) {
                const typeData = this.data.summary.by_type;
                this.typeChart.data.labels = typeData.map(t => t.route_type);
                this.typeChart.data.datasets[0].data = typeData.map(t => t.total_usage);
                this.typeChart.update();
            }
        },

        getMethodColor(method) {
            const colors = {
                GET: 'bg-green-100 text-green-800',
                POST: 'bg-blue-100 text-blue-800',
                PUT: 'bg-yellow-100 text-yellow-800',
                DELETE: 'bg-red-100 text-red-800',
                PATCH: 'bg-purple-100 text-purple-800'
            };
            return colors[method] || 'bg-gray-100 text-gray-800';
        },

        getTypeColor(type) {
            const colors = {
                web: 'bg-blue-100 text-blue-800',
                api: 'bg-green-100 text-green-800',
                admin: 'bg-red-100 text-red-800',
                dashboard: 'bg-purple-100 text-purple-800',
                auth: 'bg-yellow-100 text-yellow-800',
                assets: 'bg-gray-100 text-gray-800'
            };
            return colors[type] || 'bg-gray-100 text-gray-800';
        },

        formatNumber(num) {
            return new Intl.NumberFormat().format(num);
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        }
    }
};

// Stats Card Component
const StatCard = {
    props: ['title', 'value', 'icon', 'color'],
    template: `
        <div class="card">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-md flex items-center justify-center"
                         :class="'bg-' + color + '-100 text-' + color + '-600'">
                        <i :class="icon" class="text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            {{ title }}
                        </dt>
                        <dd class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ formatValue(value) }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    `,
    methods: {
        formatValue(value) {
            return new Intl.NumberFormat().format(value);
        }
    }
};

// Create and mount the app
createApp({
    components: {
        Dashboard,
        StatCard
    }
}).mount('#app');