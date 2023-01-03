<?php

namespace Bo\PluginManager\App\Http\Controllers;

use Alert;
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

    public function remove(Request $request): bool
    {
        if ($request->has('pluginPath') && $this->plugin->remove($request->input('pluginPath'))) {
            Alert::add('success', trans("pluginmanager::pluginmanager.success_remove_plugin"))->flash();
        } else {
            Alert::add('error', trans("pluginmanager::pluginmanager.fail_remove_plugin"))->flash();
        }
        return true;
    }

    public function activate(Request $request): bool
    {
        if ($request->has('pluginPath') && $this->plugin->active($request->input('pluginPath'))) {
            Alert::add('success', trans("pluginmanager::pluginmanager.success_active_plugin"))->flash();
        } else {
            Alert::add('error', trans("pluginmanager::pluginmanager.fail_active_plugin"))->flash();
        }
        return true;
    }

    public function deactivate(Request $request): bool
    {
        if ($request->has('pluginPath') && $this->plugin->deactivate($request->input('pluginPath'))) {
            Alert::add('success', trans("pluginmanager::pluginmanager.success_deactivate_plugin"))->flash();
        } else {
            Alert::add('error', trans("pluginmanager::pluginmanager.fail_deactivate_plugin"))->flash();
        }
        return true;
    }
}
