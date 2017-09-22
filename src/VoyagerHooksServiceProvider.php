<?php

namespace Larapack\VoyagerHooks;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Larapack\Hooks\HooksServiceProvider;

class VoyagerHooksServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        // Register the HooksServiceProvider
        $this->app->register(HooksServiceProvider::class);

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager-hooks');
    }

    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function boot(Dispatcher $events)
    {
        if (config('voyager-hooks.add-route', true)) {
            $events->listen('voyager.admin.routing', [$this, 'addHookRoute']);
        }
    }

    public function addHookRoute($router)
    {
        $namespacePrefix = '\\Larapack\\VoyagerHooks\\Controllers\\';

        $router->get('hooks', ['uses' => $namespacePrefix.'HooksController@index', 'as' => 'hooks']);
        $router->get('hooks/{name}/enable', ['uses' => $namespacePrefix.'HooksController@enable', 'as' => 'hooks.enable']);
        $router->get('hooks/{name}/disable', ['uses' => $namespacePrefix.'HooksController@disable', 'as' => 'hooks.disable']);
        $router->get('hooks/{name}/update', ['uses' => $namespacePrefix.'HooksController@update', 'as' => 'hooks.update']);
        $router->post('hooks', ['uses' => $namespacePrefix.'HooksController@install', 'as' => 'hooks.install']);
        $router->delete('hooks/{name}', ['uses' => $namespacePrefix.'HooksController@uninstall', 'as' => 'hooks.uninstall']);
    }
}
