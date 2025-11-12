<?php

namespace NMehroj\RouteUsageTracker\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use NMehroj\RouteUsageTracker\Middleware\TrackRouteUsage;
use NMehroj\RouteUsageTracker\Models\RouteUsage;
use Orchestra\Testbench\TestCase;

class TrackRouteUsageTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            \NMehroj\RouteUsageTracker\RouteUsageTrackerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /** @test */
    public function it_tracks_route_usage()
    {
        // Create a mock request and route
        $request = Request::create('/test', 'GET');
        $route = new Route(['GET'], '/test', []);
        $route->setName('test.route');
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        // Create middleware instance
        $middleware = new TrackRouteUsage();

        // Execute middleware
        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert route usage was tracked
        $this->assertDatabaseHas('route_usage', [
            'route_name' => 'test.route',
            'route_path' => '/test',
            'method' => 'GET',
            'usage_count' => 1,
        ]);

        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_increments_usage_count_for_existing_routes()
    {
        // Create initial usage record
        RouteUsage::create([
            'route_name' => 'test.route',
            'route_path' => '/test',
            'method' => 'GET',
            'usage_count' => 1,
            'first_used_at' => now(),
            'last_used_at' => now(),
        ]);

        // Create request
        $request = Request::create('/test', 'GET');
        $route = new Route(['GET'], '/test', []);
        $route->setName('test.route');
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        // Execute middleware
        $middleware = new TrackRouteUsage();
        $middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert usage count was incremented
        $this->assertDatabaseHas('route_usage', [
            'route_name' => 'test.route',
            'method' => 'GET',
            'usage_count' => 2,
        ]);
    }
}
