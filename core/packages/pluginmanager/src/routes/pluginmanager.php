<?php

Route::group([
    'namespace'  => 'Bo\PluginManager\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', bo_middleware()],
], function () {
    Route::get(config('bo.pluginmanager.route'), 'PluginManagerController@index')->name('plugin.index');
});
