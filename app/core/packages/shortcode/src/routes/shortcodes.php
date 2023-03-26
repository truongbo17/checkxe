<?php

Route::group([
    'namespace'  => 'Bo\Shortcode\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', bo_middleware()],
], function () {
    Route::crud('shortcode', 'ShortcodeController');
});
