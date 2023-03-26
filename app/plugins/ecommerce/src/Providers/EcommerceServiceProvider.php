<?php

namespace Bo\Ecommerce\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class EcommerceServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("ecommerce")
            ->setPrimaryKeyPlugin("ecommerce")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerGroup('ecommerce')
            ->setLabel('Ecommerce')
            ->setPosition(10)
            ->setIcon('nav-icon las la-shopping-cart')
            ->render();

        \SideBarDashBoard::registerItem('ec-category')
            ->setLabel('Category')
            ->setPosition(1)
            ->setRoute(bo_url('ec-category'))
            ->setGroup('ecommerce')
            ->setIcon('nav-icon la la-list')
            ->render();

        \SideBarDashBoard::registerItem('ec-product')
            ->setLabel('Product')
            ->setPosition(2)
            ->setRoute(bo_url('ec-product'))
            ->setGroup('ecommerce')
            ->setIcon('nav-icon las la-archive')
            ->render();
    }
}
