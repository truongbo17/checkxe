<?php

namespace Bo\PluginManager\App\Services;

interface PluginInterface
{
    public function active(string $plugin_path);

    public function remove(string $plugin_path);

    public function deactivate(string $plugin_path);
}
