<?php

namespace Bo\Handbook\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class HandbookServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("handbook")
            ->setPrimaryKeyPlugin("handbook")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('handbook')
            ->setLabel('Handbook')
            ->setPosition(10)
            ->setRoute(bo_url('handbook'))
            ->setIcon('nav-icon lar la-question-circle')
            ->render();
    }
}
