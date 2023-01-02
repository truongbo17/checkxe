<?php

namespace Bo\PluginManager\App\Http\Controllers;

use Bo\PluginManager\App\Services\PluginInterface;

class PluginManagerController
{
    private PluginInterface $plugin;

    public function __construct(PluginInterface $plugin)
    {
        $this->plugin = $plugin;
    }

    public function index()
    {
        $data['title'] = trans('pluginmanager::pluginmanager.name');
        $data['plugins'] = $this->plugin->getAllPlugin();

        return view('pluginmanager::pluginmanager', $data);
    }

    public function remove()
    {

    }

    public function active()
    {

    }

    public function deactivate()
    {

    }
}
