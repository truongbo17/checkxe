<?php

namespace Bo\PluginManager\App\Http\Controllers;

use Bo\PluginManager\App\Services\PluginInterface;
use Illuminate\Http\Request;

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

    public function remove(Request $request)
    {
        if ($request->has('pluginPath')) {
            if ($this->plugin->remove($request->input('pluginPath'))) {
                return json_encode([
                    "error"   => false,
                    "message" => trans("pluginmanager::pluginmanager.success_remove_plugin")
                ]);
            }
        }
        return json_encode([
            "error"   => true,
            "message" => trans("pluginmanager::pluginmanager.fail_remove_plugin")
        ]);
    }

    public function active()
    {

    }

    public function deactivate()
    {

    }
}
