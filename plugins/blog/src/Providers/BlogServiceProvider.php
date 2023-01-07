<?php

namespace Bo\Blog\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Exception;
use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("blog")
            ->setPrimaryKeyPlugin("blog")
            ->loadRoutes(["web"])
            ->loadHelper();
    }

    /**
     * @throws Exception
     */
    public function boot()
    {
        $this
            ->loadMigration()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews();

        \SideBarDashBoard::registerGroup('blog')
            ->setLabel('Blog')
            ->setPosition(4)
            ->setIcon('nav-icon la la-newspaper-o')
            ->render();

        \SideBarDashBoard::registerItem('article')
            ->setLabel('Article')
            ->setPosition(1)
            ->setRoute(bo_url('article'))
            ->setGroup('blog')
            ->setIcon('nav-icon la la-newspaper-o')
            ->render();

        \SideBarDashBoard::registerItem('category')
            ->setLabel('Category')
            ->setPosition(2)
            ->setRoute(bo_url('category'))
            ->setGroup('blog')
            ->setIcon('nav-icon la la-list')
            ->render();

        \SideBarDashBoard::registerItem('tag')
            ->setLabel('Tag')
            ->setPosition(3)
            ->setRoute(bo_url('tag'))
            ->setGroup('blog')
            ->setIcon('nav-icon la la-tag')
            ->render();
    }
}
