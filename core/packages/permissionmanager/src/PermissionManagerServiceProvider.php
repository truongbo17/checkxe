<?php

namespace Bo\PermissionManager;

use Bo\Base\App\Repositories\Caches\RoleCacheDecorator;
use Bo\Base\App\Repositories\Eloquent\RoleRepository;
use Bo\Base\App\Repositories\Interfaces\RoleInterface;
use Bo\PermissionManager\App\Models\Role;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PermissionManagerServiceProvider extends ServiceProvider
{
    /**
     * Where the route file lives, both inside the package and in the app (if overwritten).
     *
     * @var string
     */
    public $routeFilePath = '/routes/bo/permissionmanager.php';

    /**
     * path database migration
     * @var string $routeFilePath
     * */
    private string $migrationFilePath = '/database/migrations/2022_08_21_014423_create_permission_tables.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__ . '/config/permissionmanager.php',
            'bo.permissionmanager'
        );

        // define the routes for the application
        $this->setupRoutes($this->app->router);

        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views/crud'), 'crud');

        // load migration
        $this->loadMigrationsFrom(__DIR__ . $this->migrationFilePath);

        \SideBarDashBoard::registerGroup('permission_manager')
            ->setLabel('Authentication')
            ->setPosition(98)
            ->setIcon('nav-icon la la-users')
            ->render();

        \SideBarDashBoard::registerItem('user')
            ->setLabel('Users')
            ->setPosition(1)
            ->setRoute(bo_url('user'))
            ->setIcon('nav-icon la la-user')
            ->setGroup('permission_manager')
            ->render();

        \SideBarDashBoard::registerItem('role')
            ->setLabel('Roles')
            ->setPosition(2)
            ->setRoute(bo_url('role'))
            ->setIcon('nav-icon la la-id-badge')
            ->setGroup('permission_manager')
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
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //Bind repository

    }
}
