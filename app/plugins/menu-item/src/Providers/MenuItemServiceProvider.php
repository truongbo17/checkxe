<?php

namespace Bo\MenuItem\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class MenuItemServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("menu-item")
            ->setPrimaryKeyPlugin("menu-item")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('menu-item')
            ->setLabel('Menu-item')
            ->setPosition(2)
            ->setRoute(bo_url('menu-item'))
            ->setIcon('nav-icon las la-bars')
            ->render();
    }
}
