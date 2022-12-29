<?php

namespace Bo\FileManager;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/elfinder.php',
            'elfinder'
        );

        if (! Config::get('elfinder.route.prefix')) {
            Config::set('elfinder.route.prefix', Config::get('bo.base.route_prefix').'/elfinder');
        }

        // - then the stock views that come with the package, in case a published view might be missing
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views/vendor/elfinder'), 'elfinder');

        \SideBarDashBoard::registerItem('file_manager')
            ->setLabel('File Manager')
            ->setPosition(1)
            ->setRoute(bo_url('elfinder'))
            ->setIcon('nav-icon la la-files-o')
            ->setGroup('advanced')
            ->render();
    }
}
