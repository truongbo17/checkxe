<?php

namespace Bo\Generators\Providers;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    protected array $commands = [
        \Bo\Generators\Console\Commands\ConfigBoCommand::class,
        \Bo\Generators\Console\Commands\RequestBoCommand::class,
        \Bo\Generators\Console\Commands\TestCommand::class,
        \Bo\Generators\Console\Commands\BoCrudCommand::class,
        \Bo\Generators\Console\Commands\ModelBoCommand::class,
        \Bo\Generators\Console\Commands\ControllerBoCommand::class,
        \Bo\Generators\Console\Commands\RouteBoCommand::class,
    ];

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
