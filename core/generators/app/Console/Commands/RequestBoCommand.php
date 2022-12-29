<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class RequestBoCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bo:dashboard:request';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:dashboard:request
    {plugin_name : Plugin name}
    {--request_name= : if don\'t have request_name , model name will be created with plugin_name}
    {--type= : type of model (admin or default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Request for BoCMS';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Type of command
     *
     * @var array
     * */
    protected array $type_of = [
        'admin',
        'default',
    ];

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/request.stub';
    }

    /**
     * Handle make request
     * */
    public function handle()
    {
        $plugin_name = $this->argument('plugin_name');
        $request_name = $this->option('request_name');

        $type = $this->option('type') ?? 'default';

        if (!in_array($type, $this->type_of)) {
            $this->error("Type of controller must match in [admin or default]");
            return false;
        }

        if (exist_plugin($plugin_name)) {
            $plugin_root_data = get_file_data_by_json(exist_plugin($plugin_name));

            if ($request_name) {
                $class_request = $this->generateClassName($type, $plugin_root_data['namespace'], $plugin_name, $request_name);
            } else {
                $class_request = $this->generateClassName($type, $plugin_root_data['namespace'], $plugin_name);
            }

            $this->makeFileByClassName($class_request);

            return self::SUCCESS;
        } else {
            $this->error("Plugin $plugin_name don't exist!");
            return false;
        }
    }

    /**
     * Check exist class and path name and make directory
     *
     * @param array $class_request
     * @param bool $autoload (default : true)
     *
     * @return bool
     * */
    public function makeFileByClassName(array $class_request, bool $autoload = true): bool
    {
        //Generate
        if (!class_exists($class_request['class_name'], $autoload)) {
            if (!$this->files->exists($class_request['path'])) {
                $this->makeDirectory($class_request['path']);
                $this->files->put($class_request['path'], $this->sortImports($this->buildClassCustom($class_request)));

                $this->info($this->type . ' ' . $class_request['name'] . ' created successfully.');
                //In BoCrudCommand , use regex check and get class name
                $this->info("|{$class_request['class_name']}|");

                return true;
            }else{
                //Path file in exist,check namespace class
                $file = $this->files->get($class_request['path']);

                //check namespace
                if (!Str::contains($file, "namespace {$class_request['namespace']}")) {
                    $this->comment("Namespace not match with plugin request.Please make new Request use namespace {$class_request['namespace']} of Plugin");
                    return false;
                }

                $this->info("Request file and class exist !!!");
                //In BoCrudCommand , use regex check and get class name
                $this->info("|{$class_request['class_name']}|");
                return true;
            }
        } else {
            $this->error("Class name {$class_request['name']} exist!");
            //In BoCrudCommand , use regex check and get class name
            $this->info("|{$class_request['class_name']}|");
            return false;
        }
    }

    /**
     * Generate Class Name
     *
     * @param string $type
     * @param string $namespace
     * @param string $plugin_name
     * @param string $request_name
     *
     * @return array
     * */
    public function generateClassName(string $type, string $namespace, string $plugin_name, string $request_name = ''): array
    {
        $type = ($type == 'admin') ? 'Admin\\' : '';

        if (mb_strlen($request_name) > 0) {
            $request = ucfirst($request_name) . "Request";
        } else {
            $request = ucfirst($plugin_name) . "Request";
        }
        $class_request = $namespace . "Http\\Requests\\" . $type . $request;

        return [
            'name'       => $request,
            'class_name' => $class_request,
            'namespace'  => $namespace . "Http\\Requests",
            'path'       => path_plugins_request($plugin_name, $request, $type),
        ];
    }

    /**
     * Build the class with the given name.
     *
     * @param array $class_request
     * @return string
     */
    protected function buildClassCustom(array $class_request): string
    {
        $stub = $this->files->get($this->getStub());

        return $this
            ->replaceNamespace($stub, $class_request['class_name'])
            ->replaceClass($stub, $class_request['name']);
    }
}
