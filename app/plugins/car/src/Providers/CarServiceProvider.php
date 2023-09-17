<?php

namespace Bo\Car\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class CarServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("car")
            ->setPrimaryKeyPlugin("car")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('car')
            ->setLabel('Phương tiện tai nạn')
            ->setPosition(10)
            ->setRoute(bo_url('car'))
            ->setIcon('nav-icon las la-car-crash')
            ->render();
    }
}
