<?php

namespace Bo\LogManager;

use Illuminate\Log\LogManager;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Route;

class LogManagerServiceProvider extends ServiceProvider
{
    /**
     * Where the route file lives, both inside the package and in the app (if overwritten).
     *
     * @var string
     */
    public $routeFilePath = '/routes/logmanager.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views'), 'logmanager');

        $this->loadTranslationsFrom(realpath(__DIR__ . '/../resources/lang'), 'bo.logmanager');

        \SideBarDashBoard::registerGroup('advanced')
            ->setLabel('Advanced')
            ->setPosition(97)
            ->setIcon('nav-icon la la-cogs')
            ->render();

        \SideBarDashBoard::registerItem('log_manager')
            ->setLabel('Logs')
            ->setPosition(2)
            ->setRoute(bo_url('log'))
            ->setIcon('nav-icon las la-terminal')
            ->setGroup('advanced')
            ->render();
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
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__.$this->routeFilePath;

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLogManager();
        $this->setupRoutes($this->app->router);
    }

    private function registerLogManager()
    {
        $this->app->bind('logmanager', function ($app) {
            return new LogManager($app);
        });
    }
}
