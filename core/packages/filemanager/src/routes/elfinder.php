<?php

Route::group([
    'namespace'  => 'Bo\Settings\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', bo_middleware()],
], function () {
    Route::get('elfinder', function (){
        return view('elfinder::admin_elfinder');
    })->name('admin.elfinder');
});
