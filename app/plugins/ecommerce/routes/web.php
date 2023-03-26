<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('bo.base.web_middleware', 'web'),
        (array) config('bo.base.middleware_key', 'admin')
    ),
    'namespace'  => 'Bo\Ecommerce\Http\Controllers',
], function () { // custom admin routes
    Route::crud('ec-category', 'CategoryCrudController');
    Route::crud('ec-product', 'ProductCrudController');
}); // this should be the absolute last line of this file
