<?php

namespace Bo\Settings;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Bo\Settings\App\Models\Setting;
use Config;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * path route
     * @var string $routeFilePath
     * */
    private string $routeFilePath = '/routes/settings.php';

    /**
     * path database migration
     * @var string $routeFilePath
     * */
    private string $migrationFilePath = '/database/migrations/2022_08_13_131614_create_settings_table.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__ . '/config/setting.php',
            'bo.setting'
        );

        // define the routes for the application
        $this->setupRoutes($this->app->router);

        $this->loadMigrationsFrom(__DIR__ . $this->migrationFilePath);

        // only use the Settings package if the Settings table is present in the database
        if (!\App::runningInConsole() && Schema::hasTable(config('bo.setting.table_name'))) {
            // get all settings from the database
            $settings = Setting::all();

            // bind all settings to the Laravel config, so you can call them like
            // Config::get('setting_contact_email')
            foreach ($settings as $key => $setting) {
                Config::set($setting->type . '_' . $setting->key, $setting->value);
            }
        }

        \SideBarDashBoard::registerGroup('setting_group')
            ->setLabel('Settings')
            ->setPosition(99)
            ->setIcon('nav-icon la la-cog')
            ->render();

        \SideBarDashBoard::registerItem('setting')
            ->setLabel('General')
            ->setPosition(1)
            ->setRoute(bo_url('setting'))
            ->setIcon('nav-icon las la-archive')
            ->setGroup('setting_group')
            ->render();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // register their aliases
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Setting', \Bo\Settings\App\Models\Setting::class);
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
