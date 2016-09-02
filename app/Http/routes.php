<?php
Route::any('/ssss/banners/uploadImage', function () {
    return ' wachiiiin';
});
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

Route::auth();

Route::get('/article/{id}', ['as' => 'article.show', 'uses' => 'ArticleController@show']);

/**
 * Admin routes.
 */
Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {
    Route::get('/', ['as' => 'admin.dashboard', 'uses' => 'Admin\AdminController@index']);

    /**
     * Article Sections Routes.
     */
    Route::resource('/articles/sections', 'Admin\ArticlesSectionsController');

    /**
     * Articles Routes.
     */
    Route::post('/articles/uploadImage', ['as' => 'admin.articles.uploadImage', 'uses' => 'Admin\ArticlesController@uploadImage']);
    Route::resource('/articles', 'Admin\ArticlesController');

    /**
     * Banners Routes.
     * Note: Used `banners-uploadImage` instead of `banners/uploadImage` because AdBlocker blacklists this.
     */
    Route::post('banners-uploadImage', ['as' => 'admin.banners.uploadImage', 'uses' => 'Admin\BannersController@uploadImage']);
    Route::resource('banners', 'Admin\BannersController');

    Route::get('settings', ['as' => 'admin.tools.settings', 'uses' => 'Admin\ToolsController@settings']);
    Route::post('settings', ['as' => 'admin.tools.settings.post', 'uses' => 'Admin\ToolsController@postSettings']);
});

Route::get('foo', function () {
    $image = Image::make('http://placehold.it/500x500/030/e8117f');
    return Response::make($image->encode('jpg'), 200, ['Content-Type' => 'image/jpeg']);
});