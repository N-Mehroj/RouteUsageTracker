<?php

namespace NMehroj\RouteUsageTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishDashboardCommand extends Command
{
    protected $signature = 'route-usage-tracker:publish-dashboard';

    protected $description = 'Publish Route Usage Tracker dashboard assets';

    public function handle()
    {
        $this->info('Publishing Route Usage Tracker Inertia.js dashboard assets...');

        // Create directories if they don't exist
        $jsPath = resource_path('js/Pages/RouteUsageTracker');
        $componentsPath = resource_path('js/Pages/RouteUsageTracker/Components');
        $layoutsPath = resource_path('js/Layouts');

        if (!File::exists($jsPath)) {
            File::makeDirectory($jsPath, 0755, true);
        }

        if (!File::exists($componentsPath)) {
            File::makeDirectory($componentsPath, 0755, true);
        }

        if (!File::exists($layoutsPath)) {
            File::makeDirectory($layoutsPath, 0755, true);
        }

        // Copy Dashboard Vue component
        $sourceDashboard = __DIR__ . '/../../resources/js/Pages/RouteUsageTracker/Dashboard.vue';
        $targetDashboard = $jsPath . '/Dashboard.vue';

        if (File::exists($sourceDashboard)) {
            File::copy($sourceDashboard, $targetDashboard);
            $this->info('✅ Dashboard Vue component published');
        }

        // Copy StatCard component
        $sourceStatCard = __DIR__ . '/../../resources/js/Pages/RouteUsageTracker/Components/StatCard.vue';
        $targetStatCard = $componentsPath . '/StatCard.vue';

        if (File::exists($sourceStatCard)) {
            File::copy($sourceStatCard, $targetStatCard);
            $this->info('✅ StatCard component published');
        }

        // Copy Layout component
        $sourceLayout = __DIR__ . '/../../resources/js/Layouts/AppLayout.vue';
        $targetLayout = $layoutsPath . '/AppLayout.vue';

        if (File::exists($sourceLayout)) {
            File::copy($sourceLayout, $targetLayout);
            $this->info('✅ Layout component published');
        }

        $this->info('🎉 Inertia.js dashboard assets published successfully!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('1. Make sure Inertia.js is installed in your Laravel application');
        $this->line('2. Install required dependencies:');
        $this->line('   npm install vue@^3.3.0 @inertiajs/vue3 chart.js @heroicons/vue @vueuse/core');
        $this->line('3. Add the dashboard route to your web.php:');
        $this->line('   Route::get(\'/route-usage-dashboard\', [\\NMehroj\\RouteUsageTracker\\Controllers\\DashboardController::class, \'index\']);');
        $this->line('4. Visit /route-usage-dashboard in your browser');
        $this->line('5. The dashboard uses Inertia.js for seamless SPA experience!');

        return Command::SUCCESS;
    }
}
