<?php

if (!function_exists('plugin_path')) {
    function plugin_path(string $plugin_dir_name = ""): string
    {
        return base_path("plugins/" . $plugin_dir_name);
    }
}

if (!function_exists('get_path_src_plugin')) {
    function get_path_src_plugin(string $plugin_dir_name = ""): string
    {
        return base_path("plugins/" . $plugin_dir_name . "/src/");
    }
}

if (!function_exists('get_path_route_plugin')) {
    function get_path_route_plugin(string $plugin_dir_name, string $file_name = ""): string
    {
        if (empty($file_name)) {
            return base_path("plugins/" . $plugin_dir_name . "/routes");
        }
        return base_path("plugins/" . $plugin_dir_name . "/routes/" . $file_name . ".php");
    }
}

if (!function_exists('get_path_database_plugin')) {
    function get_path_database_plugin(string $plugin_dir_name, string $file_name = ""): string
    {
        if (empty($file_name)) {
            return base_path("plugins/" . $plugin_dir_name . "/database/migrations");
        }
        return base_path("plugins/" . $plugin_dir_name . "/database/migrations/" . $file_name . ".php");
    }
}

if (!function_exists('get_path_config_plugin')) {
    function get_path_config_plugin(string $plugin_dir_name, string $file_name = ""): string
    {
        if (empty($file_name)) {
            return base_path("plugins/" . $plugin_dir_name . "/config");
        }
        return base_path("plugins/" . $plugin_dir_name . "/config/" . $file_name . ".php");
    }
}

if (!function_exists('get_path_helper_plugin')) {
    function get_path_helper_plugin(string $plugin_dir_name, string $file_name = ""): string
    {
        if (empty($file_name)) {
            return base_path("plugins/" . $plugin_dir_name . "/helpers");
        }
        return base_path("plugins/" . $plugin_dir_name . "/helpers/" . $file_name . ".php");
    }
}

if (!function_exists('get_path_public_plugin')) {
    function get_path_public_plugin(string $plugin_dir_name, string $file_name = ""): string
    {
        if (empty($file_name)) {
            return base_path("plugins/" . $plugin_dir_name . "/public");
        }
        return base_path("plugins/" . $plugin_dir_name . "/public/" . $file_name . ".php");
    }
}

if (!function_exists('get_path_resource_plugin')) {
    function get_path_resource_plugin(string $plugin_dir_name, string $file_name = ""): string
    {
        if (empty($file_name)) {
            return base_path("plugins/" . $plugin_dir_name . "/resources");
        }
        return base_path("plugins/" . $plugin_dir_name . "/resources/" . $file_name . ".php");
    }
}


if (!function_exists('plugin_exist')) {
    function plugin_exist(string $plugin_key): bool
    {
        return (new \Bo\PluginManager\App\Services\Plugin())->exists($plugin_key);
    }
}

if (!function_exists('is_plugin_active')) {
    function is_plugin_active(string $plugin_key): bool
    {
        return (new \Bo\PluginManager\App\Services\Plugin())->is_activate($plugin_key);
    }
}
