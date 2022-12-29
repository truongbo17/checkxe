<?php

/*
|--------------------------------------------------------------------------
| Bo\PermissionManager Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Bo\PermissionManager package.
|
*/

Route::group([
    'namespace'  => 'Bo\PermissionManager\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', bo_middleware()],
], function () {
    Route::crud('role', 'RoleCrudController');
    Route::crud('user', 'UserCrudController');

    // add not_check to last string name => not check this route
//    Route::get('test',fn()=>1)->name('test.not_check');

    Route::post('user/{id}/update_status_admin', 'UserCrudController@updateStatusAdmin')->name('user.change_status_admin');
});
