<?php

namespace NMehroj\RouteUsageTracker\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SetupDashboardCommand extends Command
{
    protected $signature = 'route-usage-tracker:setup
                          {--force : Force reinstall packages}
                          {--skip-npm : Skip NPM package installation}
                          {--skip-migration : Skip running migrations}';

    protected $description = 'Complete setup for Route Usage Tracker dashboard with all dependencies';

    public function handle()
    {
        $this->info('🚀 Setting up Route Usage Tracker Dashboard...');
        $this->newLine();

        // Step 1: Run migrations
        if (!$this->option('skip-migration')) {
            $this->runMigrations();
        }

        // Step 2: Install NPM packages
        if (!$this->option('skip-npm')) {
            $this->installNpmPackages();
        }

        // Step 3: Publish dashboard assets
        $this->publishAssets();

        // Step 4: Create example files
        $this->createExampleFiles();

        $this->newLine();
        $this->info('✅ Route Usage Tracker Dashboard setup completed successfully!');
        $this->newLine();
        $this->displayNextSteps();

        return Command::SUCCESS;
    }

    protected function runMigrations()
    {
        $this->info('📊 Running Route Usage Tracker migrations...');

        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->line('✅ Migrations completed successfully');
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            if (!$this->confirm('Continue setup without migrations?')) {
                return Command::FAILURE;
            }
        }

        $this->newLine();
    }

    protected function installNpmPackages()
    {
        $this->info('📦 Installing NPM packages...');

        // Check if package.json exists
        $packageJsonPath = base_path('package.json');
        if (!File::exists($packageJsonPath)) {
            $this->warn('⚠️  package.json not found. Creating basic package.json...');
            $this->createPackageJson();
        }

        // Required packages
        $packages = [
            'vue@^3.3.0',
            '@inertiajs/vue3',
            'chart.js@^4.4.0',
            '@heroicons/vue@^2.0.0',
            '@vueuse/core@^10.5.0'
        ];

        $devPackages = [
            '@vitejs/plugin-vue@^4.4.0',
            '@tailwindcss/forms@^0.5.2'
        ];

        // Install production dependencies
        $this->installPackages($packages, false);

        // Install dev dependencies
        $this->installPackages($devPackages, true);

        $this->line('✅ All NPM packages installed successfully');
        $this->newLine();
    }

    protected function installPackages(array $packages, bool $isDev = false)
    {
        $command = ['npm', 'install'];

        if ($isDev) {
            $command[] = '--save-dev';
            $this->line('Installing dev dependencies: ' . implode(', ', $packages));
        } else {
            $this->line('Installing dependencies: ' . implode(', ', $packages));
        }

        $command = array_merge($command, $packages);

        try {
            $process = new Process($command, base_path());
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->line('✅ Packages installed: ' . implode(', ', $packages));
        } catch (ProcessFailedException $e) {
            $this->error('❌ Failed to install packages: ' . $e->getMessage());
            $this->line('You can manually install with: npm install ' . implode(' ', $packages));
        }
    }

    protected function publishAssets()
    {
        $this->info('📁 Publishing dashboard assets...');

        try {
            Artisan::call('route-usage-tracker:publish-dashboard');
            $this->line('✅ Dashboard assets published successfully');
        } catch (\Exception $e) {
            $this->error('❌ Failed to publish assets: ' . $e->getMessage());
        }

        $this->newLine();
    }

    protected function createPackageJson()
    {
        $packageJson = [
            'private' => true,
            'scripts' => [
                'build' => 'vite build',
                'dev' => 'vite dev'
            ],
            'devDependencies' => [
                'axios' => '^1.1.2',
                'laravel-vite-plugin' => '^0.8.0',
                'vite' => '^4.0.0'
            ]
        ];

        File::put(base_path('package.json'), json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->line('✅ Created basic package.json');
    }

    protected function createExampleFiles()
    {
        $this->info('📄 Creating example configuration files...');

        // Create vite.config.js if it doesn't exist
        $viteConfigPath = base_path('vite.config.js');
        if (!File::exists($viteConfigPath)) {
            $this->createViteConfig();
        }

        // Create or update app.js for Inertia.js
        $this->updateAppJs();

        $this->line('✅ Example files created/updated');
        $this->newLine();
    }

    protected function createViteConfig()
    {
        $viteConfig = <<<'JS'
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
JS;

        File::put(base_path('vite.config.js'), $viteConfig);
        $this->line('✅ Created vite.config.js');
    }

    protected function updateAppJs()
    {
        $appJsPath = resource_path('js/app.js');

        if (!File::exists($appJsPath)) {
            // Create new app.js for Inertia.js
            $appJsContent = <<<'JS'
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
JS;

            File::put($appJsPath, $appJsContent);
            $this->line('✅ Created app.js for Inertia.js');
        } else {
            $this->line('ℹ️  app.js already exists, please manually configure for Inertia.js');
        }
    }

    protected function displayNextSteps()
    {
        $this->line('<fg=cyan>Next Steps:</fg=cyan>');
        $this->line('');

        $this->line('1. <fg=yellow>Add route to your web.php:</fg=yellow>');
        $this->line('   <fg=green>Route::get(\'/dashboard\', [\\NMehroj\\RouteUsageTracker\\Controllers\\DashboardController::class, \'index\']);</fg=green>');
        $this->line('');

        $this->line('2. <fg=yellow>Build assets:</fg=yellow>');
        $this->line('   <fg=green>npm run build</fg=green>  (for production)');
        $this->line('   <fg=green>npm run dev</fg=green>   (for development)');
        $this->line('');

        $this->line('3. <fg=yellow>Visit your dashboard:</fg=yellow>');
        $this->line('   <fg=green>http://your-app.com/dashboard</fg=green>');
        $this->line('');

        $this->line('4. <fg=yellow>Setup Inertia.js middleware (if not already done):</fg=yellow>');
        $this->line('   <fg=green>php artisan inertia:middleware</fg=green>');
        $this->line('');

        $this->line('<fg=cyan>API Endpoints available:</fg=cyan>');
        $this->line('• /route-usage-tracker/api/summary');
        $this->line('• /route-usage-tracker/api/routes');
        $this->line('• /route-usage-tracker/api/daily-usage');
        $this->line('• /route-usage-tracker/api/export');
    }
}
