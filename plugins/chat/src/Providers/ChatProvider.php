<?php

namespace Bo\Chat\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class ChatProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("chat")
            ->setPrimaryKeyPlugin("chat")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerItem('chat')
            ->setLabel('Chat')
            ->setPosition(10)
            ->setRoute(bo_url('chat'))
            ->setIcon('nav-icon lar la-question-circle')
            ->render();
    }
}
