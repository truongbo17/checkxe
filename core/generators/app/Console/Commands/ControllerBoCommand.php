<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ControllerBoCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bo:dashboard:controller';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:dashboard:controller
    {plugin_name}
    {class_name_model : class name model used with controller crud}
    {class_name_request : class name model used with controller crud}
    {--controller_name : if don\'t have controller_name , controller name will be created with plugin_name}
    {--type= : type of model (admin or default)}
    {--force : if you add this option, once the model exists it will not add the CrudTrait trait (default is false)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a BoCMS CRUD controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

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
        return __DIR__ . '/../../stubs/controller.stub';
    }

    /**
     * @var string $class_name_model
     * */
    protected string $class_name_model;

    /**
     * @var string $class_name_request
     * */
    protected string $class_name_request;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $plugin_name = $this->argument('plugin_name');
        $controller_name = $this->option('controller_name');
        $this->class_name_model = $this->argument('class_name_model');
        $this->class_name_request = $this->argument('class_name_request');
        $type = $this->option('type') ?? 'default';

        if (!in_array($type, $this->type_of)) {
            $this->error("Type of controller must match in [admin or default]");
            return false;
        }

        if (exist_plugin($plugin_name)) {
            $plugin_root_data = get_file_data_by_json(exist_plugin($plugin_name));

            if ($controller_name) {
                $class_name_model = $this->generateClassName($type, $plugin_root_data['namespace'], $plugin_name, $controller_name);
            } else {
                $class_name_model = $this->generateClassName($type, $plugin_root_data['namespace'], $plugin_name);
            }

            $this->makeFileByClassName($class_name_model);

            return self::SUCCESS;
        } else {
            $this->error("Plugin $plugin_name don't exist!");
            return false;
        }
    }

    /**
     * Generate Class Name
     *
     * @param string $type
     * @param string $namespace
     * @param string $plugin_name
     * @param string $controller_name
     *
     * @return array
     * */
    public function generateClassName(string $type, string $namespace, string $plugin_name, string $controller_name = ''): array
    {
        $type = ($type == 'admin') ? 'Admin\\' : '';

        if (mb_strlen($controller_name) > 0) {
            $controller = ucfirst($controller_name) . "Controller";
        } else {
            $controller = ucfirst($plugin_name) . "Controller";
        }
        $class_name_controller = $namespace . "Http\\Controllers\\" . $type . $controller;

        return [
            'name'       => $controller,
            'class_name' => $class_name_controller,
            'namespace'  => $namespace . "Http\\Controllers",
            'path'       => path_plugins_controller($plugin_name, $controller, $type),
        ];
    }

    /**
     * Check exist class and path name and make directory
     *
     * @param array $class_controller
     * @param bool $autoload (default : true)
     *
     * @return bool|mixed
     * */
    public function makeFileByClassName(array $class_controller, bool $autoload = true)
    {
        //Generate
        if (!class_exists($class_controller['class_name'], $autoload)) {
            if (!$this->files->exists($class_controller['path'])) {
                $this->makeDirectory($class_controller['path']);
                $this->files->put($class_controller['path'], $this->sortImports($this->buildClassCustom($class_controller)));

                $this->info($this->type . ' ' . $class_controller['name'] . ' created successfully.');

                //In BoCrudCommand , use regex check and get class name
                $this->info("|{$class_controller['class_name']}|");
                return true;
            } else {
                $file = $this->files->get($class_controller['path']);
                //check namespace
                if (!Str::contains($file, "namespace {$class_controller['namespace']}")) {
                    $this->comment("Namespace not match with plugin controller.Please make new Request use namespace {$class_controller['namespace']} of Plugin");
                    return false;
                }

                //In BoCrudCommand , use regex check and get class name
                $this->info("|{$class_controller['class_name']}|");
            }
        } else {
            $this->error("Class name " . $class_controller['name'] . " or path " . $class_controller['path'] . " exist! Check class Request and Model");

            $this->info("|{$class_controller['class_name']}|");
            return false;
        }
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return ControllerBoCommand
     */
    protected function replaceNameStrings(string &$stub, string $name): ControllerBoCommand
    {
        $nameTitle = Str::afterLast(preg_replace("/Controller$/", "", $name), '\\');
        $nameKebab = Str::kebab($nameTitle);
        $nameSingular = str_replace('-', ' ', $nameKebab);
        $namePlural = Str::plural($nameSingular);

        $stub = str_replace('DummyClassModel', $this->class_name_model, $stub);
        $stub = str_replace('DummyClassRequest', $this->class_name_request, $stub);
        $stub = str_replace('DummyClassController', $name, $stub);
        $stub = str_replace('dummy-class', $nameKebab, $stub);
        $stub = str_replace('dummy singular', $nameSingular, $stub);
        $stub = str_replace('dummy plural', $namePlural, $stub);

        return $this;
    }

    /**
     * Build the class with the given name.
     *
     * @param array $class_controller
     * @return string
     */
    protected function buildClassCustom(array $class_controller): string
    {
        $stub = $this->files->get($this->getStub());

        $this->replaceNamespace($stub, $class_controller['class_name'])
            ->replaceNameStrings($stub, $class_controller['name'])
            ->replaceSetFromDb($stub);

        return $stub;
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param string $stub
     * @return ControllerBoCommand
     */
    protected function replaceSetFromDb(string &$stub): ControllerBoCommand
    {
        if (!class_exists($this->class_name_model)) {
            return $this;
        }

        $attributes = $this->getAttributes($this->class_name_model);

        // create an array with the needed code for defining fields
        $fields = \Arr::except($attributes, ['id', 'created_at', 'updated_at', 'deleted_at']);
        $fields = collect($fields)
            ->map(function ($field) {
                return "CRUD::field('$field');";
            })
            ->toArray();

        // create an array with the needed code for defining columns
        $columns = \Arr::except($attributes, ['id']);
        $columns = collect($columns)
            ->map(function ($column) {
                return "CRUD::column('$column');";
            })
            ->toArray();

        // replace setFromDb with actual fields and columns
        $stub = str_replace('CRUD::setFromDb(); // fields', implode(PHP_EOL . '        ', $fields), $stub);
        $stub = str_replace('CRUD::setFromDb(); // columns', implode(PHP_EOL . '        ', $columns), $stub);

        return $this;
    }

    /**
     * Get attributes model
     *
     * @param string $model
     *
     * @return mixed
     * */
    protected function getAttributes(string $model)
    {
        $attributes = [];
        $model = new $model;

        // if fillable was defined, use that as the attributes
        if (count($model->getFillable())) {
            $attributes = $model->getFillable();
        } else {
            // otherwise, if guarded is used, just pick up the columns straight from the bd table
            $attributes = \Schema::getColumnListing($model->getTable());
        }

        return $attributes;
    }
}
