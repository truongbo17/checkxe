<?php

namespace Bo\FileManager;

use Bo\FileManager\Console\Commands\Install;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{

    protected array $commands = [
        Install::class,
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // - then the stock views that come with the package, in case a published view might be missing
//        $this->loadViewsFrom(realpath(__DIR__ . '/../resources/views/vendor/elfinder'), 'elfinder');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views/vendor/elfinder' => resource_path('views/vendor/elfinder'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../public/themes/elfinder.theme.css' => public_path('packages/backpack/filemanager/themes/elfinder.theme.css'),
            ], 'public');

            $this->publishes([
                __DIR__ . '/config/elfinder.php' => config_path('elfinder.php'),
            ], 'public');

            $this->commands($this->commands);
        }

        \SideBarDashBoard::registerItem('file_manager')
            ->setLabel('File Manager')
            ->setPosition(1)
            ->setRoute(bo_url('elfinder'))
            ->setIcon('nav-icon la la-files-o')
            ->setGroup('advanced')
            ->render();
    }
}
