<?php

namespace Bo\Blog\Providers;

use Bo\Base\Services\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this
            ->setDirPlugin("blog")
            ->setPrimaryKeyPlugin("blog")
            ->loadRoutes(["web"]);
    }
}
