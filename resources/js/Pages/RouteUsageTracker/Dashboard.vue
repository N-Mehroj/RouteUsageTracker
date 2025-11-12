<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div class="flex items-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              Route Usage Tracker
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <button
              @click="toggleTheme"
              class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
            >
              <component :is="isDark ? SunIcon : MoonIcon" class="h-5 w-5 text-gray-600 dark:text-gray-300" />
            </button>
            
            <!-- Export Button -->
            <button
              @click="exportData"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Export CSV
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Summary Cards -->
      <div class="px-4 py-6 sm:px-0">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <StatCard
            title="Total Routes"
            :value="summary.total_routes || 0"
            icon="ChartBarIcon"
            color="bg-blue-500"
          />
          <StatCard
            title="Total Requests"
            :value="summary.total_requests || 0"
            icon="CursorArrowRaysIcon"
            color="bg-green-500"
          />
          <StatCard
            title="Most Used Route"
            :value="summary.most_used_route?.route_name || 'N/A'"
            :subtitle="`${summary.most_used_route?.usage_count || 0} requests`"
            icon="FireIcon"
            color="bg-red-500"
          />
          <StatCard
            title="Active Today"
            :value="summary.today_active_routes || 0"
            icon="ClockIcon"
            color="bg-purple-500"
          />
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-8">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Filters</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Route Type</label>
              <select
                v-model="filters.type"
                @change="applyFilters"
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              >
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
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">HTTP Method</label>
              <select
                v-model="filters.method"
                @change="applyFilters"
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">All Methods</option>
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="PATCH">PATCH</option>
                <option value="DELETE">DELETE</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
              <input
                v-model="filters.search"
                @input="debounceFilter"
                type="text"
                placeholder="Search routes..."
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
              <select
                v-model="filters.dateRange"
                @change="applyFilters"
                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              >
                <option value="">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Daily Usage Chart -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Daily Usage Trend</h3>
            <canvas ref="dailyChart" class="w-full h-64"></canvas>
          </div>
          
          <!-- Route Types Chart -->
          <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Routes by Type</h3>
            <canvas ref="typeChart" class="w-full h-64"></canvas>
          </div>
        </div>

        <!-- Routes Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Route Statistics</h3>
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
                    Type
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
                <tr v-for="route in filteredRoutes" :key="`${route.route_name}-${route.method}`">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-white font-medium">
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
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getTypeClass(route.route_type)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                      {{ route.route_type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ route.usage_count.toLocaleString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(route.last_used_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div v-if="routes.length > itemsPerPage" class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing {{ ((currentPage - 1) * itemsPerPage) + 1 }} to {{ Math.min(currentPage * itemsPerPage, routes.length) }} of {{ routes.length }} results
              </div>
              <div class="flex space-x-2">
                <button
                  @click="currentPage--"
                  :disabled="currentPage === 1"
                  class="px-3 py-1 text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50"
                >
                  Previous
                </button>
                <button
                  @click="currentPage++"
                  :disabled="currentPage >= totalPages"
                  class="px-3 py-1 text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50"
                >
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useStorage } from '@vueuse/core'
import {
  SunIcon,
  MoonIcon,
  ChartBarIcon,
  CursorArrowRaysIcon,
  FireIcon,
  ClockIcon
} from '@heroicons/vue/24/outline'
import Chart from 'chart.js/auto'
import StatCard from './Components/StatCard.vue'

// Props from Laravel controller
const { props } = usePage()
const { summary, topRoutes, recentActivity, apiEndpoints } = props

// Reactive data
const isDark = useStorage('theme-dark', false)
const routes = ref(topRoutes || [])
const filteredRoutes = ref([])
const currentPage = ref(1)
const itemsPerPage = ref(20)
const isLoading = ref(false)

// Filters
const filters = ref({
  type: '',
  method: '',
  search: '',
  dateRange: ''
})

// Charts
const dailyChart = ref(null)
const typeChart = ref(null)
let dailyChartInstance = null
let typeChartInstance = null

// Computed properties
const totalPages = computed(() => Math.ceil(filteredRoutes.value.length / itemsPerPage.value))
const paginatedRoutes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredRoutes.value.slice(start, end)
})

// Methods
const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
}

const loadRoutes = async () => {
  if (isLoading.value) return
  
  isLoading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.method) params.append('method', filters.value.method)
    if (filters.value.search) params.append('search', filters.value.search)
    if (filters.value.dateRange) params.append('period', filters.value.dateRange)
    
    const response = await fetch(`${apiEndpoints.routes}?${params}`)
    const data = await response.json()
    routes.value = data
    filterRoutes()
  } catch (error) {
    console.error('Failed to load routes:', error)
  } finally {
    isLoading.value = false
  }
}

const filterRoutes = () => {
  let filtered = [...routes.value]
  
  if (filters.value.type) {
    filtered = filtered.filter(route => route.route_type === filters.value.type)
  }
  
  if (filters.value.method) {
    filtered = filtered.filter(route => route.method === filters.value.method)
  }
  
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    filtered = filtered.filter(route => 
      (route.route_name && route.route_name.toLowerCase().includes(search)) ||
      (route.route_path && route.route_path.toLowerCase().includes(search))
    )
  }
  
  filteredRoutes.value = filtered
  currentPage.value = 1
}

const applyFilters = () => {
  loadRoutes()
}

let debounceTimeout = null
const debounceFilter = () => {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    filterRoutes()
  }, 300)
}

const exportData = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.method) params.append('method', filters.value.method)
    if (filters.value.dateRange) params.append('period', filters.value.dateRange)
    
    const response = await fetch(`${apiEndpoints.export}?${params}`)
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `route-usage-${new Date().toISOString().split('T')[0]}.csv`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Export failed:', error)
  }
}

const loadDailyUsage = async () => {
  try {
    const response = await fetch(`${apiEndpoints.dailyUsage}?days=30`)
    const data = await response.json()
    updateDailyChart(data)
  } catch (error) {
    console.error('Failed to load daily usage:', error)
  }
}

const loadTypeStats = async () => {
  try {
    const response = await fetch(apiEndpoints.typeStats)
    const data = await response.json()
    updateTypeChart(data)
  } catch (error) {
    console.error('Failed to load type stats:', error)
  }
}

const updateDailyChart = (data) => {
  if (dailyChartInstance) {
    dailyChartInstance.destroy()
  }
  
  const ctx = dailyChart.value.getContext('2d')
  dailyChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: data.map(item => item.date),
      datasets: [{
        label: 'Daily Requests',
        data: data.map(item => item.count),
        borderColor: 'rgb(59, 130, 246)',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: {
            color: isDark.value ? '#f3f4f6' : '#374151'
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: isDark.value ? '#9ca3af' : '#6b7280'
          },
          grid: {
            color: isDark.value ? '#374151' : '#e5e7eb'
          }
        },
        y: {
          ticks: {
            color: isDark.value ? '#9ca3af' : '#6b7280'
          },
          grid: {
            color: isDark.value ? '#374151' : '#e5e7eb'
          }
        }
      }
    }
  })
}

const updateTypeChart = (data) => {
  if (typeChartInstance) {
    typeChartInstance.destroy()
  }
  
  const ctx = typeChart.value.getContext('2d')
  typeChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: data.map(item => item.route_type),
      datasets: [{
        data: data.map(item => item.total_usage),
        backgroundColor: [
          '#3b82f6', '#ef4444', '#10b981', '#f59e0b',
          '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: isDark.value ? '#f3f4f6' : '#374151',
            padding: 20
          }
        }
      }
    }
  })
}

const getMethodClass = (method) => {
  const classes = {
    'GET': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    'POST': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
    'PUT': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
    'PATCH': 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100',
    'DELETE': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
  }
  return classes[method] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
}

const getTypeClass = (type) => {
  const classes = {
    'web': 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100',
    'api': 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100',
    'admin': 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
    'dashboard': 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
    'auth': 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
    'assets': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
  }
  return classes[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100'
}

const formatDate = (dateString) => {
  if (!dateString) return 'Never'
  return new Date(dateString).toLocaleString()
}

// Lifecycle
onMounted(() => {
  document.documentElement.classList.toggle('dark', isDark.value)
  filterRoutes()
  
  nextTick(() => {
    loadDailyUsage()
    loadTypeStats()
  })
})

// Watch for theme changes
watch(isDark, (newValue) => {
  document.documentElement.classList.toggle('dark', newValue)
  // Recreate charts with new theme
  if (dailyChartInstance || typeChartInstance) {
    nextTick(() => {
      loadDailyUsage()
      loadTypeStats()
    })
  }
})
</script>