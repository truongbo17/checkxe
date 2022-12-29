<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BoCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:dashboard:crud
    {plugin_name : plugin name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a CRUD interface: Controller, Model, Request, Route For Dashboard BoCMS';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $plugin_name = (string)$this->argument('plugin_name');
        $plugin_name_title = ucfirst(Str::camel($plugin_name));
        $plugin_name_kebab = Str::kebab($plugin_name_title);
        $plugin_name_plural = ucwords(str_replace('-', ' ', Str::plural($plugin_name_kebab)));

        // Create the CRUD Model and show output
        \Artisan::call('bo:dashboard:model', [
            'plugin_name' => $plugin_name,
        ]);
        $class_name_model = $this->getClassByRegex(\Artisan::output());
        if (!$class_name_model) {
            $this->error("Something went wrong, please check and create new your Model !!!");
            return false;
        }

        // Create the CRUD Request and show output
        \Artisan::call('bo:dashboard:request', [
            'plugin_name' => $plugin_name,
            '--type'      => 'admin',
        ]);
        $class_name_request = $this->getClassByRegex(\Artisan::output());
        if (!$class_name_request) {
            $this->error("Something went wrong, please check and create new your Request !!!");
            return false;
        }

        // Create the CRUD Controller and show output
        \Artisan::call('bo:dashboard:controller',
            [
                'plugin_name'        => $plugin_name,
                'class_name_model'   => $class_name_model,
                'class_name_request' => $class_name_request,
                '--type'             => 'admin',
            ]);
        $class_name_controller = $this->getClassByRegex(\Artisan::output());
        if (!$class_name_controller) {
            $this->error("Something went wrong, please check and create new your Controller !!!");
            return false;
        }

        // Create the CRUD Controller and show output
        \Artisan::call('bo:dashboard:route',
            [
                'plugin_name'        => $plugin_name,
                '--route_option'     => 'admin',
                '--class_controller' => $class_name_controller,
            ]);
        $this->info(\Artisan::output());

        // Create the sidebar item
        \Artisan::call('bo:dashboard:add-sidebar-content',
            [
                'code' => "<li class=\"nav-item\"><a class=\"nav-link\" href=\"{{ bo_url('$plugin_name') }}\"><i class=\"nav-icon la la-question\"></i> $plugin_name_plural</a></li>",
            ]);
        $this->info(\Artisan::output());

        // if the application uses cached routes, we should rebuild the cache so the previous added route will
        // be acessible without manually clearing the route cache.
        if (app()->routesAreCached()) {
            $this->call('route:cache');
        }

        $this->info("Create CRUD Admin plugin {$plugin_name} success !");
    }

    /**
     * Return class name by output command using regex (check by |class_name|)
     *
     * @param string $output_command
     *
     * @return string|false
     * */
    public function getClassByRegex(string $output_command)
    {
        preg_match("/\|.+\|/", $output_command, $matches);
        if (isset($matches[0])) {
            $this->info($output_command);
            return str_replace("|", "", $matches[0]);
        }

        $this->warn($output_command);
        return false;
    }
}
