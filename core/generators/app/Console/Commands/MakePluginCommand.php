<?php

namespace Bo\Generators\Console\Commands;

use Bo\PluginManager\App\Services\Plugin;
use Bo\PluginManager\App\Services\PluginInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePluginCommand extends Command
{
    private PluginInterface $plugin;

    public function __construct(Plugin $plugin)
    {
        parent::__construct();
        $this->plugin = $plugin;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:make:plugin
    {plugin_name : Plugin name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create plugin for BoCMS';

    /**
     * Execute the console command.
     *
     * @return int|void
     */
    public function handle()
    {
        $plugin_name = (string)$this->argument('plugin_name');
        $plugin_name_title = ucfirst(Str::camel($plugin_name));
        $plugin_name_kebab = Str::kebab($plugin_name_title);
        $plugin_name_plural = Str::plural($plugin_name_kebab);
        $plugin_name_plural_up_case = ucwords(str_replace('-', ' ', Str::plural($plugin_name_kebab)));
        $plugin_path = plugin_path($plugin_name);

//        dd($plugin_name,$plugin_name_title,$plugin_name_kebab,$plugin_name_plural,$plugin_name_plural_up_case,$plugin_path);

//        if (plugin_exist($plugin_name)) {
//            $this->error("Plugin exists in {$this->plugin->getPlugin($plugin_name)['path']}");
//            return self::FAILURE;
//        }
//
//        if(File::isDirectory($plugin_path)){
//            $this->error("Folder $plugin_path already exists, please delete folder or try again with another name");
//            return self::FAILURE;
//        }

        //Make config
        $this->call('bo:cms:config', [
            'plugin_name' => $plugin_name,
            'name' => 'general',
            '--make_with_plugin'
        ]);

        //Make helper
        $this->call('bo:cms:helper', [
            'plugin_name' => $plugin_name,
            'name' => 'helper',
            '--make_with_plugin'
        ]);

        //Make migration
        $this->call('bo:cms:migration', [
            'plugin_name' => $plugin_name,
            "name" => "$plugin_name_plural",
            "--make_with_plugin"
        ]);

        // if the application uses cached routes, we should rebuild the cache so the previous added route will
        // be acessible without manually clearing the route cache.
        if (app()->routesAreCached()) {
            $this->call('route:cache');
        }

        $this->info("Make plugin \"{$plugin_name}\" in path \"{$plugin_path}\" success !");
    }
}
