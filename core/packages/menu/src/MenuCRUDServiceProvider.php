<?php

namespace Bo\MenuCRUD;

use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MenuCRUDServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Where the route file lives, both inside the package and in the app (if overwritten).
     *
     * @var string
     */
    public $routeFilePath = '/routes/menucrud.php';

    /**
     * path database migration
     * @var string $routeFilePath
     * */
    private string $migrationFilePath = '/database/migrations';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . $this->migrationFilePath);

        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'menucrud');

        \SideBarDashBoard::registerGroup('menu')
            ->setLabel('Menu')
            ->setPosition(2)
            ->setIcon('nav-icon las la-list-ul')
            ->render();

        \SideBarDashBoard::registerItem('menu')
            ->setLabel('Menu')
            ->setPosition(1)
            ->setRoute(bo_url('menu'))
            ->setIcon('nav-icon las la-bars')
            ->setGroup('menu')
            ->render();

        \SideBarDashBoard::registerItem('menu-item')
            ->setLabel('Menu Item')
            ->setPosition(2)
            ->setRoute(bo_url('menu-item'))
            ->setIcon('nav-icon las la-stream')
            ->setGroup('menu')
            ->render();
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__.$this->routeFilePath;

        // but if there's a file with the same name in routes/bo, use that one
        if (file_exists(base_path().$this->routeFilePath)) {
            $routeFilePathInUse = base_path().$this->routeFilePath;
        }

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->setupRoutes($this->app->router);

        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });
    }
}
