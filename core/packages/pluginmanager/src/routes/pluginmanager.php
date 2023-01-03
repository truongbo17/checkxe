<?php

Route::group([
    'namespace'  => 'Bo\PluginManager\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', bo_middleware()],
], function () {
    Route::get(config('bo.pluginmanager.route'), 'PluginManagerController@index')->name('plugin.index');
    Route::post(config('bo.pluginmanager.route') . '/remove', 'PluginManagerController@remove')->name('plugin.remove');
    Route::post(config('bo.pluginmanager.route') . '/deactivate', 'PluginManagerController@deactivate')->name('plugin.deactivate');
    Route::post(config('bo.pluginmanager.route') . '/activate', 'PluginManagerController@activate')->name('plugin.activate');
});
