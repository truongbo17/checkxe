<?php

// Admin Interface Routes
Route::group([
    'namespace'  => 'Bo\LangFileManager\App\Http\Controllers',
    'prefix'     => config('bo.base.route_prefix', 'admin'),
    'middleware' => ['web', config('bo.base.middleware_key', 'admin')],
], function () {
    // Language
    Route::get('language/texts/{lang?}/{file?}', 'LanguageCrudController@showTexts')->name('language.text.show');
    Route::post('language/texts/{lang}/{file}', 'LanguageCrudController@updateTexts')->name('language.text.update');
    Route::crud('language', 'LanguageCrudController');
});
