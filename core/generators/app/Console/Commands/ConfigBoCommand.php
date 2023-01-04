<?php

namespace Bo\Generators\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ConfigBoCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'bo:dashboard:config';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bo:cms:config
    {plugin_name : plugin name}
    {name : config file name}
    {--make_with_plugin : force check plugin exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a config file plugin for BoCMS';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Config';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/config.stub';
    }

    /**
     * Handle create config file
     *
     * @return false|int
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $name = $this->getNameInput();
        $path = $this->getPath($name);
        $plugin_name = $this->argument('plugin_name');

        if (!plugin_exist($plugin_name) && $this->option('make_with_plugin')) {
            $this->error("Plugin does not exist");
            return self::FAILURE;
        }

        if ($this->checkExits($path)) {
            $this->error("$this->type $name already existed!");
            return false;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info("$this->type created successfully in " . realpath($path));

        return self::SUCCESS;
    }

    /**
     * Check exist file or directory
     *
     * @param string $path_name
     * @return bool
     */
    public function checkExits(string $path_name): bool
    {
        return $this->files->exists($path_name);
    }

    /**
     * Get path file
     *
     * @param string $name
     * @param string $plugin_name => if there is a value to include, it could be the path to the plugin directory
     * @return string
     */
    public function getPath($name, string $plugin_name = ''): string
    {
        return $this->laravel['path'] . '/../' . $plugin_name . '/config/' . str_replace('\\', '/', $name) . '.php';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        return $this->files->get($this->getStub());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [

        ];
    }
}
