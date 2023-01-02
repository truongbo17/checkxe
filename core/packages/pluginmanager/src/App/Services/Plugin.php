<?php

namespace Bo\PluginManager\App\Services;

use Alert;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class Plugin implements PluginInterface
{
    /** @var array $plugins */
    private array $plugins = [];

    /** @var array $activated_plugins */
    private array $activated_plugins = [];

    /**
     * @throws FileNotFoundException
     */
    public function __construct(File $file)
    {
        $plugins = $this->scanFolder(plugin_path()) ?? [];
        $activated_plugins = $this->getActivatedPluginFromJsonFile();

        foreach ($plugins as $plugin) {
            if (
                File::isDirectory(plugin_path($plugin)) &&
                File::exists(plugin_path($plugin . DIRECTORY_SEPARATOR . config('bo.pluginmanager.file_plugin')))
            ) {
                if (File::exists(plugin_path($plugin . '/.DS_Store'))) {
                    File::delete(plugin_path($plugin . '/.DS_Store'));
                }

                try {
                    $content = File::get(plugin_path($plugin . DIRECTORY_SEPARATOR . config('bo.pluginmanager.file_plugin')));
                    if (is_string($content)) {
                        $content = json_decode($content, true);
                    }
                } catch (FileNotFoundException $e) {
                    $content = [];
                }

                if (is_array($content) && count($content) > 0 && $this->validateContent($content, plugin_path($plugin))) {
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
                        $this->plugins[] = $content;
                    } else {
                        Alert::error(trans('pluginmanager::pluginmanager.validate_key_content', ['plugin_path' => plugin_path($plugin)]));
                    }
                }
            }
        }
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
            $json = File::get(core_package_path("pluginmanager/" . config('bo.pluginmanager.file_active_plugin')));
            if (is_string($json)) {
                return json_decode($json, true);
            }
        } catch (\Exception $exception) {
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

    public function active()
    {
        // TODO: Implement active() method.
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    public function deactivate()
    {
        // TODO: Implement deactivate() method.
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
}
