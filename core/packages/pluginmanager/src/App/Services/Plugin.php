<?php

namespace Bo\PluginManager\App\Services;

use Alert;
use Bo\Base\Services\BaseService;
use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Plugin implements PluginInterface
{
    /** @var array $plugins */
    private array $plugins = [];

    /** @var array $activated_plugins */
    private array $activated_plugins = [];

    /**
     * @throws FileNotFoundException
     */
    public function __construct()
    {
        $plugins = $this->scanFolder(plugin_path("")) ?? [];
        $activated_plugins = $this->getActivatedPluginFromJsonFile();

        foreach ($plugins as $plugin) {
            if (
                File::isDirectory(plugin_path($plugin)) &&
                File::exists(plugin_path($plugin, config('bo.pluginmanager.file_plugin')))
            ) {
                if (File::exists(plugin_path($plugin . '/.DS_Store'))) {
                    File::delete(plugin_path($plugin . '/.DS_Store'));
                }

                $content = BaseService::getJsonDataPlugin($plugin);

                if (count($content) > 0 && $this->validateContent($content, plugin_path($plugin))) {
                    if ($this->checkExistPlugin($content)) {
                        if (File::exists(plugin_path($plugin . '/screenshot.png'))) {
                            $content['image'] = base64_encode(File::get(plugin_path($plugin . '/screenshot.png')));
                        } else {
                            $content['image'] = base64_encode(File::get(core_package_path("pluginmanager/default-image-plugin.png")));
                        }

                        if (in_array($content["primary_key"], $activated_plugins)) {
                            $this->activated_plugins[] = $content["primary_key"];
                            $content["active"] = true;
                        } else {
                            $content["active"] = false;
                        }

                        $content['path'] = plugin_path($plugin);
                        $this->plugins[$content['primary_key']] = $content;
                    } else {
                        Alert::error(trans('pluginmanager::pluginmanager.validate_key_content', ['plugin_path' => plugin_path($plugin)]));
                    }
                }
            }
        }

        $this->updateActivatedPlugins();
    }

    public function updateActivatedPlugins(): bool
    {
        try {
            if (File::exists(core_package_path("pluginmanager/" . config('bo.pluginmanager.file_active_plugin')))) {
                return File::put(core_package_path("pluginmanager/" . config('bo.pluginmanager.file_active_plugin')), json_encode($this->activated_plugins ?? []));
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
        return false;
    }

    /**
     * @param string $path
     * @param array $ignoreFiles
     * @return array
     */
    public function scanFolder(string $path, array $ignoreFiles = []): array
    {
        try {
            if (File::isDirectory($path)) {
                $data = array_diff(scandir($path), array_merge(['.', '..', '.DS_Store'], $ignoreFiles));
                natsort($data);
                return $data;
            }
        } catch (Exception $exception) {
            Log::error($exception);
        }
        return [];
    }

    /**
     * Get plugin activated in json file
     *
     * @return array
     * */
    private function getActivatedPluginFromJsonFile(): array
    {
        try {
            if (File::exists(core_package_path("pluginmanager/" . config('bo.pluginmanager.file_active_plugin')))) {
                $json = File::get(core_package_path("pluginmanager/" . config('bo.pluginmanager.file_active_plugin')));
                if (is_string($json)) {
                    return json_decode($json, true) ?? [];
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception);
        }
        return [];
    }

    /**
     * Validate key content data from json file plugin
     *
     * @param array $content
     * @param string $plugin_path
     * @return bool
     */
    public function validateContent(array $content, string $plugin_path): bool
    {
        $required = [
            "primary_key",
            "name",
            "namespace",
            "provider",
            "author",
            "url",
            "version",
            "description",
        ];
        if (count(array_intersect_key(array_flip($required), $content)) === count($required)) {
            return true;
        }
        Alert::error(trans('pluginmanager::pluginmanager.validate_key_content', ['plugin_path' => $plugin_path]));
        return false;
    }

    /**
     * Check exist plugin (check by primary_key,namespace,provider)
     *
     * @param array $content
     * @return bool
     */
    private function checkExistPlugin(array $content): bool
    {
        $content_plugins = Arr::dot($this->plugins);
        foreach ($content_plugins as $key => $value) {
            if (preg_match("/\d+\.name/", $key) && $content['primary_key'] == $value) {
                return false;
            }
            if (preg_match("/\d+\.namespace/", $key) && $content['primary_key'] == $value) {
                return false;
            }
            if (preg_match("/\d+\.provider/", $key) && $content['primary_key'] == $value) {
                return false;
            }
        }
        return true;
    }

    /**
     * Active plugin
     *
     * @paraamm string $plugin_path
     * @return bool
     */
    public function active(string $plugin_path): bool
    {
        try {
            $content = $this->getContentJsonFromPluginPath($plugin_path);
            if (count($content) > 0 && isset($this->plugins[$content['primary_key']])) {
                if (!in_array($content['primary_key'], $this->activated_plugins)) {
                    if (!class_exists($content['provider'])) {
                        $loader = new ClassLoader();
                        $loader->setPsr4($content['namespace'], $plugin_path . DIRECTORY_SEPARATOR . "src");
                        $loader->register(true);
                    }
                    if (class_exists($content['namespace'] . 'Plugin') && method_exists($content['namespace'] . 'Plugin', 'activate')) {
                        call_user_func([$content['namespace'] . 'Plugin', 'activate']);
                    }

                    $this->publishAssets($content['primary_key'], $plugin_path);
                    $this->migrate($content['primary_key']);

                    app()->register($content['provider']);

                    $this->activated_plugins[] = $content['primary_key'];
                    $this->updateActivatedPlugins();

                    if (class_exists($content['namespace'] . 'Plugin') && method_exists($content['namespace'] . 'Plugin', 'activated')) {
                        call_user_func([$content['namespace'] . 'Plugin', 'activated']);
                    }

                    BaseService::clearCache();

                    return true;

                }
            }
        } catch (Exception $e) {
            Log::error($e);
            Alert::error($e->getMessage());
        }
        return false;
    }

    /**
     * Run migrations
     **/
    private function migrate(string $plugin)
    {
        try {
            if (File::isDirectory(get_path_database_plugin($plugin))) {
                app()->make('migrator')->run(get_path_database_plugin($plugin));
            }
        } catch (Exception $exception) {
            Alert::error($exception->getMessage());
        }
    }

    /**
     * remove plugin
     *
     * @param string $plugin_path
     * @return bool
     */
    public function remove(string $plugin_path): bool
    {
        try {
            $content = $this->getContentJsonFromPluginPath($plugin_path);
            if (count($content) > 0 && isset($this->plugins[$content['primary_key']])) {
                if (in_array($content['primary_key'], $this->activated_plugins)) {
                    $this->deactivate($plugin_path);
                }

                if (!class_exists($content['provider'])) {
                    $loader = new ClassLoader();
                    $loader->setPsr4($content['namespace'], $plugin_path . DIRECTORY_SEPARATOR . "src");
                    $loader->register(true);
                }

                Schema::disableForeignKeyConstraints();
                if (class_exists($content['namespace'] . 'Plugin') && method_exists($content['namespace'] . 'Plugin', 'remove')) {
                    call_user_func([$content['namespace'] . 'Plugin', 'remove']);
                }
                Schema::enableForeignKeyConstraints();

                $migrations = [];
                foreach ($this->scanFolder($plugin_path . DIRECTORY_SEPARATOR . "/database/migrations") as $file) {
                    $migrations[] = pathinfo($file, PATHINFO_FILENAME);
                }
                DB::table('migrations')->whereIn('migration', $migrations)->delete();

                File::deleteDirectory($plugin_path);

                $this->removeModuleFiles($content['primary_key']);

                if (class_exists($content['namespace'] . 'Plugin') && method_exists($content['namespace'] . 'Plugin', 'removed')) {
                    call_user_func([$content['namespace'] . 'Plugin', 'removed']);
                }

                BaseService::clearCache();

                $this->updateActivatedPlugins();

                return true;
            }
        } catch (Exception $e) {
            Log::error($e);
            Alert::error($e->getMessage());
        }
        return false;
    }

    /**
     * Deactivate plugin
     *
     * @param string $plugin_path
     * @return bool
     */
    public function deactivate(string $plugin_path): bool
    {
        try {
            $content = $this->getContentJsonFromPluginPath($plugin_path);
            if (count($content) > 0 && isset($this->plugins[$content['primary_key']])) {
                if (!class_exists($content['provider'])) {
                    $loader = new ClassLoader();
                    $loader->setPsr4($content['namespace'], $plugin_path . DIRECTORY_SEPARATOR . "src");
                    $loader->register(true);
                }
                if (in_array($content['primary_key'], $this->activated_plugins)) {
                    if (class_exists($content['namespace'] . 'Plugin') && method_exists($content['namespace'] . 'Plugin', 'deactivate')) {
                        call_user_func([$content['namespace'] . 'Plugin', 'deactivate']);
                    }

                    if (($key = array_search($content['primary_key'], $this->activated_plugins)) !== false) {
                        unset($this->activated_plugins[$key]);

                        $this->updateActivatedPlugins();

                        if (class_exists($content['namespace'] . 'Plugin') && method_exists($content['namespace'] . 'Plugin', 'deactivated')) {
                            call_user_func([$content['namespace'] . 'Plugin', 'deactivated']);
                        }

                        BaseService::clearCache();

                        return true;
                    }
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            Alert::error($e->getMessage());
        }
        return false;
    }

    /**
     * get content json for plugin path
     *
     * @param string $plugin_path
     * @return array
     */
    private function getContentJsonFromPluginPath(string $plugin_path): array
    {
        if (
            File::isDirectory($plugin_path) &&
            File::exists($plugin_path . DIRECTORY_SEPARATOR . config('bo.pluginmanager.file_plugin'))
        ) {
            try {
                $content = File::get($plugin_path . DIRECTORY_SEPARATOR . config('bo.pluginmanager.file_plugin'));
                if (is_string($content)) {
                    return json_decode($content, true) ?? [];
                }
            } catch (Exception $e) {
                Log::error($e);
            }
        }
        return [];
    }

    /**
     * Get all plugin
     *
     * @return array
     * */
    public function getAllPlugin(): array
    {
        return $this->plugins ?? [];
    }

    /**
     * Return activated plugins
     *
     * @return array
     * */
    public function getAllPluginActivated(): array
    {
        return $this->activated_plugins ?? [];
    }

    /**
     * Return a plugin by key
     *
     * @param string $primary_key
     * @return array
     */
    public function getPlugin(string $primary_key): array
    {
        return $this->plugins[$primary_key] ?? [];
    }

    /**
     * @param string $module
     * @param string $type
     * @return void
     */
    private function removeModuleFiles(string $module, string $type = 'plugins'): void
    {
        $folders = [
            public_path('resources/' . $type . '/' . $module),
            resource_path('assets/' . $type . '/' . $module),
            resource_path('views/vendor/' . $type . '/' . $module),
            lang_path('vendor/' . $type . '/' . $module),
            config_path($type . '/' . $module),
        ];

        foreach ($folders as $folder) {
            if (File::isDirectory($folder)) {
                File::deleteDirectory($folder);
            }
        }
    }

    /**
     * Check exist plugins
     *
     * @param string $plugin_key
     * @return bool
     * */
    public function exists(string $plugin_key): bool
    {
        return array_key_exists($plugin_key, $this->plugins);
    }

    /**
     * Check exist plugins activated
     *
     * @param string $plugin_key
     * @return bool
     * */
    public function is_activate(string $plugin_key): bool
    {
        return array_key_exists($plugin_key, $this->activated_plugins);
    }

    /**
     * @param string $plugin
     * @param string $plugin_path
     * @return void
     */
    private function publishAssets(string $plugin, string $plugin_path): void
    {
        $public_path = public_path('resources/vendor/plugins');

        if (!File::isDirectory($public_path)) {
            File::makeDirectory($public_path, 0755, true);
        }

        if (File::isDirectory(plugin_path($plugin . '/public'))) {
            File::copyDirectory(plugin_path($plugin . '/public'), $public_path . '/' . $plugin);
        }
    }
}
