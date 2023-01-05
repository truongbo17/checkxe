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

        $namespace = "Bo\\$plugin_name_title";
        $class_controller = $plugin_name_title . "Controller";
        $namespace_controller = "$namespace\\Http\\Controllers\\Admin";

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

        // Create plugin file json
        if (!$this->createPluginFile($plugin_name, $plugin_name_title)) return self::FAILURE;

        //Make config
        $this->call('bo:cms:config', [
            'plugin_name'        => $plugin_name,
            'name'               => 'general',
            "--make_with_plugin" => true
        ]);

        //Make helper
        $this->call('bo:cms:helper', [
            'plugin_name'        => $plugin_name,
            'name'               => 'helper',
            "--make_with_plugin" => true
        ]);

        //Make migration
        $this->call('bo:cms:migration', [
            'plugin_name'        => $plugin_name,
            "name"               => "$plugin_name_plural",
            "--make_with_plugin" => true
        ]);

        //Make lang
        $this->call('bo:cms:lang', [
            'plugin_name'        => $plugin_name,
            "name"               => "$plugin_name_plural",
            "lang"               => "en",
            "--make_with_plugin" => true
        ]);

        //Make view
        $this->call('bo:cms:view', [
            'plugin_name'        => $plugin_name,
            "name"               => "index.blade",
            "--make_with_plugin" => true
        ]);

        //Make route
        $this->call('bo:cms:route', [
            'plugin_name'          => $plugin_name,
            "name"                 => "web",
            "class_controller"     => $class_controller,
            'namespace_controller' => $namespace_controller,
            "--make_with_plugin"   => true
        ]);

        // if the application uses cached routes, we should rebuild the cache so the previous added route will
        // be acessible without manually clearing the route cache.
        if (app()->routesAreCached()) {
            $this->call('route:cache');
        }

        $this->info("Make plugin \"{$plugin_name}\" in path \"{$plugin_path}\" success !");
    }

    /**
     * Create plugin file json
     *
     * @param string $plugin_name
     * @param string $plugin_name_title
     * @return false
     */
    private function createPluginFile(string $plugin_name, string $plugin_name_title): bool
    {
        $path = plugin_path($plugin_name . DIRECTORY_SEPARATOR . "plugin.json");

        if (File::exists($path)) {
            $this->error("File plugin.json already existed in \"$path\" !");
            return false;
        }

        $this->makeDirectory($path);
        File::put($path, $this->getContentPluginJson($plugin_name, $plugin_name_title));

        $this->info("Plugin file created successfully in " . realpath($path));
        return true;
    }

    /**
     * Make directory
     *
     * @param string $path
     * @return void
     */
    private function makeDirectory(string $path): void
    {
        if (!File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Content get plugin json
     *
     * @param string $plugin_name
     * @param string $plugin_name_title
     * @return string
     */
    private function getContentPluginJson(string $plugin_name, string $plugin_name_title): string
    {
        if (File::exists(__DIR__ . '/../../stubs/plugin.stub')) {
            $content_file = File::get(__DIR__ . '/../../stubs/plugin.stub');
            $content_file = str_replace('plugin_name', $plugin_name, $content_file);
            $content_file = str_replace('name_title', $plugin_name_title, $content_file);
            $content_file = str_replace('namespace_plugin', str_replace(" ", "", "Bo\\\ $plugin_name_title"), $content_file);
            return str_replace('provider_plugin', str_replace(" ", "", "Bo\\\ $plugin_name_title\\\Providers\\\ {$plugin_name_title}ServiceProvider"), $content_file);
        }
        return "";
    }
}
