<?php

namespace Bo\Contact\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("contact")
            ->setPrimaryKeyPlugin("contact")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('contact')
            ->setLabel('Liên hệ')
            ->setPosition(10)
            ->setRoute(bo_url('contact'))
            ->setIcon('nav-icon las la-address-book')
            ->render();
    }
}
