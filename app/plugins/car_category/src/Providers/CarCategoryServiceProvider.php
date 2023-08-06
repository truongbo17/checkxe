<?php

namespace Bo\CarCategory\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class CarCategoryServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("car_category")
            ->setPrimaryKeyPlugin("car_category")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('car_category')
            ->setLabel('Hãng xe')
            ->setPosition(10)
            ->setRoute(bo_url('car_category'))
            ->setIcon('nav-icon las la-car-side')
            ->render();
    }
}
