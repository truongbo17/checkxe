<?php

namespace Bo\Base\Services;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;

class BaseService
{
    /**
     * Clear cache
     *
     * @return bool
     */
    public static function clearCache(): bool
    {
        Event::dispatch('cache:clearing');

        try {
            Cache::flush();
            if (!File::exists($storagePath = storage_path('framework/cache'))) {
                return true;
            }

            foreach (File::files($storagePath) as $file) {
                if (preg_match('/facade-.*\.php$/', $file)) {
                    File::delete($file);
                }
            }
        } catch (Exception $exception) {
            info($exception->getMessage());
        }

        Event::dispatch('cache:cleared');

        return true;
    }

    /**
     * Get json data plugin
     *
     * @param string $plugin
     * @return array
     */
    public static function getJsonDataPlugin(string $plugin): array
    {
        try {
            $content = File::get(plugin_path($plugin . DIRECTORY_SEPARATOR . config('bo.pluginmanager.file_plugin')));
            if (is_string($content)) {
                return json_decode($content, true);
            }
        } catch (FileNotFoundException $e) {
        }
        return [];
    }
}
