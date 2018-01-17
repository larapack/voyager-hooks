<?php

namespace Larapack\VoyagerHooks;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Larapack\Hooks\Events\Setup;
use Larapack\Hooks\HooksServiceProvider;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;
use TCG\Voyager\Models\Permission;

class VoyagerHooksServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register()
    {
        $configPath = dirname(__DIR__).'/publishable/config/voyager-hooks.php';

        $this->mergeConfigFrom($configPath, 'voyager-hooks');

        // Register the HooksServiceProvider
        $this->app->register(HooksServiceProvider::class);

        if (!$this->enabled()) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [$configPath => config_path('voyager-hooks.php')],
                'voyager-hooks-config'
            );
        }

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
        if (!$this->enabled()) {
            return;
        }

        if (config('voyager-hooks.add-route', true)) {
            $events->listen('voyager.admin.routing', [$this, 'addHookRoute']);
        }

        if (config('voyager-hooks.add-hook-menu-item', true)) {
            $events->listen(Setup::class, [$this, 'addHookMenuItem']);
        }

        if (config('voyager-hooks.add-hook-permissions', true)) {
            $events->listen(Setup::class, [$this, 'addHookPermissions']);
        }

        if (config('voyager-hooks.publish-vendor-files', true)) {
            $events->listen(Setup::class, [$this, 'publishVendorFiles']);
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

    public function addHookMenuItem()
    {
        $menu = Menu::where('name', 'admin')->first();

        if (is_null($menu)) {
            return;
        }

        $parentId = null;

        $toolsMenuItem = MenuItem::where('menu_id', $menu->id)
            ->where('title', 'Tools')
            ->first();

        if ($toolsMenuItem) {
            $parentId = $toolsMenuItem->id;
        }

        $menuItem = MenuItem::firstOrNew([
            'menu_id' => $menu->id,
            'title'   => 'Hooks',
            'url'     => '',
            'route'   => 'voyager.hooks',
        ]);

        if (!$menuItem->exists) {
            $menuItem->fill([
                'target'     => '_self',
                'icon_class' => 'voyager-hook',
                'color'      => null,
                'parent_id'  => $parentId,
                'order'      => 13,
            ])->save();
        }
    }

    public function addHookPermissions()
    {
        Permission::firstOrCreate([
            'key'        => 'browse_hooks',
            'table_name' => null,
        ]);
    }

    public function publishVendorFiles()
    {
        Artisan::call('vendor:publish', ['--provider' => static::class]);
    }

    public function enabled()
    {
        if (config('voyager-hooks.enabled', true)) {
            return config('hooks.enabled', true);
        }

        return config('voyager-hooks.enabled', true);
    }
}
