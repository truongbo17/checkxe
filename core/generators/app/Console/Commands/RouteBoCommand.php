<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class RouteBoCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bo:dashboard:route';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:dashboard:route
    {plugin_name : Plugin name}
    {--class_controller= : Class controller use in route...|}
    {--route_option= : Option type route |api,web,custom,admin...|}
    {--force : if you add this option, once the route exists it overwrite (default is false)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Route custom for BoCMS';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Route';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/route-admin.stub';
    }

    /**
     * Handle make route
     * */
    public function handle()
    {
        $plugin_name = $this->argument('plugin_name');
        $route_option = $this->option('route_option') ?? 'web';
        $class_controller = $this->option('class_controller');

        if (exist_plugin($plugin_name)) {
            $path_route = path_plugins_route($plugin_name, $route_option);

            if ($this->files->exists($path_route) && (!$this->hasOption('force') || !$this->option('force'))) {
                $this->warn("File route in {$path_route} exist !");
                return false;
            }

            $this->makeDirectory($path_route);
            $this->files->put($path_route, $this->sortImports($this->buildClassCustom($class_controller, $route_option, $plugin_name)));

            $this->info("Create route ${route_option} success !!!");

            return self::SUCCESS;
        } else {
            $this->error("Plugin $plugin_name don't exist!");
            return false;
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param string $class_controller
     * @param string $route_option
     * @param string $plugin_name
     *
     * @return string
     */
    protected function buildClassCustom(string $class_controller, string $route_option, string $plugin_name): string
    {
        $stub = $this->files->get($this->getStub());

        if ($route_option == 'admin') {
            $prefix_plugin = 'admin';
            $prefix_plugin_route = $plugin_name;
        } else {
            $prefix_plugin = $plugin_name;
            $prefix_plugin_route = '';
        }

        $namespace_controller = preg_replace("/\\\\[a-zA-Z]+Controller/", "", $class_controller);

        $stub = str_replace('prefix_plugin', $prefix_plugin, $stub);
        $stub = str_replace('plugin_route', $prefix_plugin_route, $stub);
        $stub = str_replace('namespace_plugin_controller', $namespace_controller, $stub);
        return str_replace('plugin_controller', $class_controller, $stub);
    }


}
