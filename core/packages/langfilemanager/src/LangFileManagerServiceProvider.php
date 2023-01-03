<?php

namespace Bo\LangFileManager;

use Bo\LangFileManager\App\Services\LangFiles;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LangFileManagerServiceProvider extends ServiceProvider
{
    /**
     * Where the route file lives, both inside the package and in the app (if overwritten).
     *
     * @var string
     */
    public $routeFilePath = '/routes/langfilemanager.php';
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    private string $migrationFilePath = '/database/migrations';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // - first the published/overwritten views (in case they have any changes)
        $this->loadViewsFrom(resource_path('views/vendor/bo/langfilemanager'), 'langfilemanager');
        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'langfilemanager');

        $this->loadTranslationsFrom(realpath(__DIR__ . '/resources/lang'), 'bo.langfilemanager');

        $this->loadMigrationsFrom(__DIR__ . $this->migrationFilePath);

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(__DIR__ . '/config/langfilemanager.php', 'bo.langfilemanager');

        \SideBarDashBoard::registerGroup('translations')
            ->setLabel('Translations')
            ->setPosition(96)
            ->setIcon('nav-icon la la-globe')
            ->render();

        \SideBarDashBoard::registerItem('languages')
            ->setLabel('Languages')
            ->setPosition(1)
            ->setRoute(bo_url('language'))
            ->setIcon('nav-icon la la-flag-checkered')
            ->setGroup('translations')
            ->render();

        \SideBarDashBoard::registerItem('site-texts')
            ->setLabel('Site texts')
            ->setPosition(2)
            ->setRoute(bo_url('language/texts'))
            ->setIcon('nav-icon la la-language')
            ->setGroup('translations')
            ->render();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLangFileManager();
        $this->setupRoutes($this->app->router);

        $this->app->singleton('langfile', function ($app) {
            return new LangFiles($app['config']['app']['locale']);
        });
    }

    private function registerLangFileManager()
    {
        $this->app->bind('langfilemanager', function ($app) {
            return new LangFileManagerServiceProvider($app);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;
        // but if there's a file with the same name in routes / bo, use that one
        if (file_exists(base_path() . $this->routeFilePath)) {
            $routeFilePathInUse = base_path() . $this->routeFilePath;
        }
        $this->loadRoutesFrom($routeFilePathInUse);
    }
}
