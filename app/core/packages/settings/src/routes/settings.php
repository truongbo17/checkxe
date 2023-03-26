<?php

Route::group([
    'namespace'  => 'Bo\Settings\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', bo_middleware()],
], function () {
    Route::crud(config('bo.setting.route'), 'SettingController');
});
