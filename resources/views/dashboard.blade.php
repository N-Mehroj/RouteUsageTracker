<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Usage Tracker Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fallback for CDN if Vite not available -->
    @if(!app()->environment('local') && !function_exists('vite'))
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @else
        <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [v-cloak] {
            display: none !important;
        }

        .dark-theme {
            @apply bg-gray-900 text-white;
        }

        .card {
            @apply bg-white dark:bg-gray-800 rounded-lg shadow-md p-6;
        }

        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors;
        }

        .btn-secondary {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition-colors;
        }
    </style>
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
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div id="app" v-cloak>
        <dashboard></dashboard>
    </div>

    <!-- Vue.js Dashboard Script - Inline for package compatibility -->
    <script type="module">
        // Dashboard script will be loaded inline here if needed
        // Or we can use a different approach
        console.log('🔍 Dashboard loading...');
        
        // For now, show a simple message if the dashboard component isn't loaded
        if (typeof window.Vue !== 'undefined') {
            const { createApp, ref, computed, onMounted } = Vue;
            
            createApp({
                setup() {
                    const message = ref('Route Usage Tracker Dashboard');
                    const isLoading = ref(true);
                    
                    onMounted(() => {
                        setTimeout(() => {
                            isLoading.value = false;
                        }, 1000);
                    });
                    
                    return {
                        message,
                        isLoading
                    };
                }
            }).mount('#app');
        }
    </script>
</body>

</html>
