<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('bo.base.web_middleware', 'web'),
        (array) config('bo.base.middleware_key', 'admin')
    ),
    'namespace'  => 'Bo\Blog\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('blogs', 'Bo\Blog\Http\Controllers\Admin\BlogsController');
}); // this should be the absolute last line of this file
