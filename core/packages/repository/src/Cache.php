<?php

namespace Bo\Repository;

use Exception;
use File;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository;
use Illuminate\Support\Arr;
use Psr\SimpleCache\InvalidArgumentException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Cache implements CacheInterface
{
    /**
     * @var string
     */
    protected $cacheGroup;

    /**
     * @var CacheManager
     */
    protected $cache;

    /**
     * @var array
     */
    protected $config;

    /**
     * Cache constructor.
     * @param Repository|CacheManager $cache
     * @param string|null $cacheGroup
     * @param array $config
     */
    public function __construct(CacheManager $cache, ?string $cacheGroup, array $config = [])
    {
        $this->cache = $cache;
        $this->cacheGroup = $cacheGroup;
        $this->config = !empty($config) ? $config : [
            'cache_time'  => 15,
            'stored_keys' => storage_path('cache_keys.json'),
        ];
    }

    /**
     * Retrieve data from cache.
     *
     * @param string $key Cache item key
     * @return mixed
     */
    public function get($key)
    {
        if (!file_exists($this->config['stored_keys'])) {
            return null;
        }

        return $this->cache->get($this->generateCacheKey($key));
    }

    /**
     * @param string $key
     * @return string
     */
    public function generateCacheKey(string $key): string
    {
        return md5($this->cacheGroup) . $key;
    }

    /**
     * Add data to the cache.
     *
     * @param string $key Cache item key
     * @param mixed $value The data to store
     * @param boolean $minutes The number of minutes to store the item
     * @return bool
     */
    public function put($key, $value, $minutes = false): bool
    {
        if (!$minutes) {
            $minutes = $this->config['cache_time'];
        }

        $key = $this->generateCacheKey($key);

        $this->storeCacheKey($key);

        $this->cache->put($key, $value, $minutes);

        return true;
    }

    /**
     * Store cache key to file
     *
     * @param string $key
     * @return bool
     * @throws FileNotFoundException
     */
    public function storeCacheKey(string $key): bool
    {
        if (file_exists($this->config['stored_keys'])) {
            $cacheKeys = $this->getFileData($this->config['stored_keys']);
            if (!empty($cacheKeys) && !in_array($key, Arr::get($cacheKeys, $this->cacheGroup, []))) {
                $cacheKeys[$this->cacheGroup][] = $key;
            }
        } else {
            $cacheKeys = [];
            $cacheKeys[$this->cacheGroup][] = $key;
        }

        BaseHelper::saveFileData($this->config['stored_keys'], $cacheKeys);

        return true;
    }

    /**
     * Test if item exists in cache
     * Only returns true if exists && is not expired.
     *
     * @param string $key Cache item key
     * @return bool If cache item exists
     *
     * @throws InvalidArgumentException
     */
    public function has($key): bool
    {
        if (!file_exists($this->config['stored_keys'])) {
            return false;
        }

        $key = $this->generateCacheKey($key);

        return $this->cache->has($key);
    }

    /**
     * Clear cache of an object
     *
     * @return bool
     * @throws FileNotFoundException
     */
    public function flush()
    {
        $cacheKeys = [];
        if (file_exists($this->config['stored_keys'])) {
            $cacheKeys = $this->getFileData($this->config['stored_keys']);
        }

        if (!empty($cacheKeys)) {
            $caches = Arr::get($cacheKeys, $this->cacheGroup);
            if ($caches) {
                foreach ($caches as $cache) {
                    $this->cache->forget($cache);
                }
                unset($cacheKeys[$this->cacheGroup]);
            }
        }

        if (!empty($cacheKeys)) {
            $this->saveFileData($this->config['stored_keys'], $cacheKeys);
        } else {
            File::delete($this->config['stored_keys']);
        }

        return true;
    }

    /**
     * @param string $file
     * @param bool $convertToArray
     * @return array|bool|mixed|null
     * @throws FileNotFoundException
     */
    public function getFileData(string $file, bool $convertToArray = true)
    {
        $file = File::get($file);
        if (!empty($file)) {
            if ($convertToArray) {
                return json_decode($file, true);
            }

            return $file;
        }

        if (!$convertToArray) {
            return null;
        }

        return [];
    }

    /**
     * @param string $path
     * @param string|array $data
     * @param bool $json
     * @return bool
     */
    public function saveFileData(string $path, $data, bool $json = true): bool
    {
        try {
            if ($json) {
                $data = $this->jsonEncodePrettify($data);
            }

            if (!File::isDirectory(File::dirname($path))) {
                File::makeDirectory(File::dirname($path), 493, true);
            }

            File::put($path, $data);

            return true;
        } catch (Exception $exception) {
            info($exception->getMessage());
            return false;
        }
    }

    /**
     * @param array|string $data
     * @return string
     */
    public function jsonEncodePrettify($data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
