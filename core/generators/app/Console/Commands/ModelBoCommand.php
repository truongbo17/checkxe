<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ModelBoCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bo:cms:model';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:cms:model
    {plugin_name}
    {name : model name}
    {--make_with_plugin : force check plugin exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Model BoCMS CRUD model';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * The trait that allows a model to have an admin panel.
     *
     * @var string
     */
    protected string $crudTrait = 'Bo\Base\Models\Traits\CrudTrait';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/model.stub';
    }

    /**
     * Table name
     *
     * @var string
     * */
    protected string $table_name;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->getNameInput();
        $plugin_name = $this->argument('plugin_name');
        $path = get_path_src_plugin($plugin_name, $name);

        if (exist_plugin($plugin_name)) {
            $plugin_root_data = get_file_data_by_json(exist_plugin($plugin_name));

            if ($model_name) {
                $class_name_model = $this->generateClassName($plugin_root_data['namespace'], $plugin_name, $model_name);
            } else {
                $class_name_model = $this->generateClassName($plugin_root_data['namespace'], $plugin_name);
            }

            $this->makeFileByClassName($class_name_model);

            return self::SUCCESS;
        } else {
            $this->error("Plugin $plugin_name don't exist!");
            return false;
        }
    }

    /**
     * Check exist class and path name and make directory
     *
     * @param array $class_model
     * @param bool $autoload (default : true)
     *
     * @return bool|string
     */
    public function makeFileByClassName(array $class_model, bool $autoload = true)
    {
        //Check exist table
        if (!$this->getTableName($class_model['name'])) {
            $this->error("Table {$this->table_name} don't exist, please run migrate create table !");
            return false;
        }

        //Generate
        if (!class_exists($class_model['class_name'], $autoload)) {
            if (!$this->files->exists($class_model['path'])) {
                $this->makeDirectory($class_model['path']);
                $this->files->put($class_model['path'], $this->sortImports($this->buildClassCustom($class_model)));

                $this->info($this->type . ' ' . $class_model['name'] . ' created successfully.');
            } else {
                if (!$this->hasOption('force') || !$this->option('force')) {
                    //If file model exist, use CrudTrait
                    $file = $this->files->get($class_model['path']);
                    $lines = preg_split('/(\r\n)|\r|\n/', $file);

                    //check namespace
                    if (!Str::contains($file, "namespace {$class_model['namespace']}")) {
                        $this->comment("Namespace not match with plugin model.Please make new Model use namespace {$class_model['namespace']} of Plugin");
                        return false;
                    }

                    //check if it already uses CrudTrait if it does, do nothing
                    if (Str::contains($file, $this->crudTrait)) {
                        $this->comment("Model {$class_model['name']} already used CrudTrait.");

                        //In BoCrudCommand , use regex check and get class name
                        $this->info("|{$class_model['class_name']}|");
                        return false;
                    }

                    // if it does not have CrudTrait, add the trait on the Model
                    foreach ($lines as $key => $line) {
                        if (Str::contains($line, "class {$class_model['name']} extends")) {
                            if (Str::endsWith($line, '{')) {
                                // add the trait on the next
                                $position = $key + 1;
                            } elseif ($lines[$key + 1] == '{') {
                                // add the trait on the next line
                                $position = $key + 2;
                            }

                            // keep in mind that the line number shown in IDEs is not
                            // the same as the array index - arrays start counting from 0,
                            // IDEs start counting from 1

                            // add CrudTrait
                            array_splice($lines, $position, 0, "    use \\{$this->crudTrait};");

                            // save the file
                            $this->files->put($class_model['path'], implode(PHP_EOL, $lines));

                            // let the user know what we've done
                            $this->info('Model already existed. Added CrudTrait to it.');
                        }
                    }
                }
            }
            //In BoCrudCommand , use regex check and get class name
            $this->info("|{$class_model['class_name']}|");
            return true;
        }

        $this->error("Class name {$class_model['name']} exist, please check and add Trait Bo\Base\Models\Traits\CrudTrait to this !");

        //In BoCrudCommand , use regex check and get class name
        $this->info("|{$class_model['class_name']}|");
        return false;
    }

    /**
     * Generate Class Name
     *
     * @param string $namespace
     * @param string $plugin_name
     * @param string $model_name
     *
     * @return array
     * */
    public function generateClassName(string $namespace, string $plugin_name, string $model_name = ''): array
    {
        if (mb_strlen($model_name) > 0) {
            $model = ucfirst($model_name);
        } else {
            $model = ucfirst($plugin_name);
        }
        $class_name_model = $namespace . "Models\\" . $model;

        return [
            'name'       => $model,
            'class_name' => $class_name_model,
            'namespace'  => $namespace . "Models",
            'path'       => path_plugins_model($plugin_name, $model),
        ];
    }

    /**
     * Replace the table name for the given stub.
     *
     * @param string $stub
     * @return ModelBoCommand
     */
    protected function replaceTable(string &$stub): ModelBoCommand
    {
        $table = Str::snake(Str::plural($this->table_name));

        $stub = str_replace('DummyTable', $table, $stub);

        return $this;
    }

    /**
     * Check exist table and return table name
     *
     * @param string $name
     *
     * @return bool
     * */
    public function getTableName(string $name): bool
    {
        $this->table_name = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', str_replace($this->getNamespace($name) . '\\', '', $name))), '_');
        return \Schema::hasTable($this->table_name);
    }

    /**
     * Build the class with the given name.
     *
     * @param array $class_model
     * @return string
     */
    protected function buildClassCustom(array $class_model): string
    {
        $stub = $this->files->get($this->getStub());

        return $this
            ->replaceNamespace($stub, $class_model['class_name'])
            ->replaceTable($stub)
            ->replaceClass($stub, $class_model['name']);
    }

}
