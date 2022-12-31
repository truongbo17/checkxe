<?php

/*
|--------------------------------------------------------------------------
| Bo\MenuCRUD Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Bo\MenuCRUD package.
|
*/

Route::group([
    'prefix' => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', config('bo.base.middleware_key', 'admin')],
    'namespace' => 'Bo\MenuCRUD\app\Http\Controllers\Admin',
], function () {
    Route::crud('menu-item', 'MenuItemCrudController');
});
