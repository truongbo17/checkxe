<?php

namespace Bo\PluginManager;

use Bo\PluginManager\App\Services\Plugin;
use Bo\PluginManager\App\Services\PluginInterface;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PluginManagerProvider extends ServiceProvider
{
    /**
     * path route
     * @var string $routeFilePath
     * */
    private string $routeFilePath = '/routes/pluginmanager.php';

    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/pluginmanager.php',
            'bo.pluginmanager'
        );

        $this->loadTranslationsFrom(realpath(__DIR__ . '/resources/lang'), 'pluginmanager');

        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'pluginmanager');

        $this->setupRoutes($this->app->router);

        $this->loadHelpers();

        \SideBarDashBoard::registerItem('plugin')
            ->setLabel(trans('pluginmanager::pluginmanager.name'))
            ->setPosition(2)
            ->setRoute(bo_url(config('bo.pluginmanager.route')))
            ->setIcon('nav-icon las la-braille')
            ->setGroup('setting_group')
            ->render();
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in package
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    private function loadHelpers()
    {
        require_once __DIR__ . '/helpers/plugins.php';
    }

    public function register()
    {
        $this->app->bind(PluginInterface::class, Plugin::class);
    }
}
