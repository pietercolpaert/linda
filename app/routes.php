<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'Tdt\Linda\Controllers\HomeController@index');

Route::get('lists/{list?}', 'Tdt\Linda\Controllers\ListsController@index')
->where('list', '[a-zA-Z0-9]+');

// Datasets and Apps
Route::resource('datasets', 'Tdt\Linda\Controllers\DatasetController');
Route::resource('apps', 'Tdt\Linda\Controllers\AppController');
Route::resource('organizations', 'Tdt\Linda\Controllers\UserController');
Route::get('users/{id?}', 'Tdt\Linda\Controllers\UserController@derefUser')
->where('id', '[0-9]+');

// Auth
Route::get('login', 'Tdt\Linda\Controllers\AuthController@getLogin');
Route::post('login', 'Tdt\Linda\Controllers\AuthController@postLogin');
Route::get('logout', 'Tdt\Linda\Controllers\AuthController@getLogout');

// Catalog
Route::get('/catalog', 'Tdt\Linda\Controllers\CatalogController@index');


App::error(function ($exception, $code) {
    // Log error
    Log::error($exception);

    // Check Accept-header
    $accept_header = \Request::header('Accept');
    $mimes = explode(',', $accept_header);

        // Create HTML response, seperate templates for status codes
    switch ($code) {
        case 404:
            return Response::view('error.404', array('exception' => $exception, 'title' => 'Oops'), 404);
        case 500:
            return Response::view('error.404', array('exception' => $exception, 'title' => 'Oops'), 500);
        default:
            return Response::view('error.404', array('exception' => $exception, 'title' => 'Oops'), 400);
    }
});
