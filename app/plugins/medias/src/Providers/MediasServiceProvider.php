<?php

namespace Bo\Medias\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class MediasServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("medias")
            ->setPrimaryKeyPlugin("medias")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('medias')
            ->setLabel('Medias')
            ->setPosition(10)
            ->setRoute(bo_url('medias'))
            ->setIcon('nav-icon las la-photo-video')
            ->render();
    }
}
