<?php

namespace Bo\Generators\Providers;

use Bo\Generators\Console\Commands\ConfigBoCommand;
use Bo\Generators\Console\Commands\HelperBoCommand;
use Bo\Generators\Console\Commands\MakePluginCommand;
use Bo\Generators\Console\Commands\MigrationBoCommand;
use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    protected array $commands = [
        MakePluginCommand::class,
        ConfigBoCommand::class,
        HelperBoCommand::class,
        MigrationBoCommand::class,
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
