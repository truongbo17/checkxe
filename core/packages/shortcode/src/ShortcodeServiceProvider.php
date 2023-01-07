<?php

namespace Bo\Shortcode;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class ShortcodeServiceProvider extends ServiceProvider
{
    /**
     * path route
     * @var string $routeFilePath
     * */
    private string $routeFilePath = '/routes/shortcodes.php';

    /**
     * path database migration
     * @var string $routeFilePath
     * */
    private string $migrationFilePath = '/database/migrations/';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . $this->migrationFilePath);

        $this->loadTranslationsFrom(realpath(__DIR__ . '/resources/lang'), 'bo');

        \SideBarDashBoard::registerItem('shortcode')
            ->setLabel('Shortcode')
            ->setPosition(4)
            ->setRoute(bo_url('shortcode'))
            ->setIcon('nav-icon las la-code')
            ->render();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->setupRoutes($this->app->router);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in package
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;

        $this->loadRoutesFrom($routeFilePathInUse);
    }
}
