<?php

namespace Bo\Base\Services;

use Exception;

trait LoadAndPublishDataTrait
{
    /** @var string|null $primary_key */
    private ?string $primary_key = null;

    /** @var string|null $dir_plugin */
    private ?string $dir_plugin = null;

    /**
     * Set primary key for plugin
     *
     * @param string $primary_key
     * @return LoadAndPublishDataTrait
     */
    public function setPrimaryKeyPlugin(string $primary_key): self
    {
        $this->primary_key = $primary_key;
        return $this;
    }

    /**
     * Set dir for plugin
     *
     * @param string $dir_plugin
     * @return LoadAndPublishDataTrait
     */
    public function setDirPlugin(string $dir_plugin): self
    {
        $this->dir_plugin = $dir_plugin;
        return $this;
    }

    /**
     * validate dir and primary key plugin not null
     *
     * @throws Exception
     */
    private function validateDirAndPrimaryKeyPlugin(): void
    {
        if (is_null($this->dir_plugin) && is_null($this->primary_key)) {
            throw new Exception("Please add dir plugin and primary_key plugin.");
        }
    }

    /**
     * load route service provider
     *
     * @param array $routes
     * @return LoadAndPublishDataTrait
     */
    public function loadRoutes(array $routes): self
    {
        $this->validateDirAndPrimaryKeyPlugin();

        foreach ($routes as $route) {
            $this->loadRoutesFrom(get_path_route_plugin($this->dir_plugin, $route));
        }

        return $this;
    }
}
