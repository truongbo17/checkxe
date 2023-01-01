<?php

namespace Bo\ReviseOperation;

use Illuminate\Support\ServiceProvider;

class ReviseOperationServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'revise-operation');

        // load views
        // - from 'resources/views/vendor/bo/revise-operation' if they're there
        // - otherwise fall back to package views
        if (is_dir(resource_path('views/vendor/bo/revise-operation'))) {
            $this->loadViewsFrom(resource_path('views/vendor/bo/revise-operation'), 'revise-operation');
        }
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'revise-operation');
    }
}
