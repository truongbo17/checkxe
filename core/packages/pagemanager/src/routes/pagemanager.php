<?php

/*
|--------------------------------------------------------------------------
| Bo\PageManager Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Bo\PageManager package.
|
*/

Route::group([
    'namespace' => '',
    'middleware' => ['web', config('bo.base.middleware_key', 'admin')],
    'prefix' => config('bo.base.route_prefix', 'admin'),
], function () {
    $controller = config('bo.pagemanager.admin_controller_class', 'Bo\PageManager\app\Http\Controllers\Admin\PageCrudController');
    Route::crud('page', $controller);
});
