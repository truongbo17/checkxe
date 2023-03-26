<?php

Route::group([
    'namespace' => 'Bo\Blog\Http\Controllers',
    'prefix' => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', 'admin'],
], function () {
    Route::crud('article', 'ArticleCrudController');
    Route::crud('category', 'CategoryCrudController');
    Route::crud('tag', 'TagCrudController');
});
