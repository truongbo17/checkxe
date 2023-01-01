<?php

if (!function_exists('plugin_path')) {
    function plugin_path($plugin_name): string
    {
        return base_path("plugins" . DIRECTORY_SEPARATOR . $plugin_name);
    }
}
