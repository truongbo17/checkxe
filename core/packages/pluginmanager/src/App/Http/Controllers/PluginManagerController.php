<?php

namespace Bo\PluginManager\App\Http\Controllers;

class PluginManagerController
{
    public function index()
    {
        $data['title'] = trans('pluginmanager::pluginmanager.name');

        return view('pluginmanager::pluginmanager', $data);
    }
}
