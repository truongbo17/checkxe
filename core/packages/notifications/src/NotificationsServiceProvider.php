<?php

namespace Bo\Notifications;

use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'bo.notifications');

        $this->mergeConfigFrom(
            __DIR__.'/config/databasenotifications.php', 'bo.notifications'
        );

        \SideBarDashBoard::pushView('bo.notifications::sidebar');
    }

    public function register()
    {
        $this->app->bind('BoNotifications', function ($app) {
            return new NotificationsServiceProvider($app);
        });
    }

    public function provides()
    {
        return [];
    }
}
