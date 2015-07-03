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
->where('list','[a-zA-Z0-9]+');

// Datasets and Apps
Route::resource('datasets', 'Tdt\Linda\Controllers\DatasetController');
Route::resource('apps', 'Tdt\Linda\Controllers\AppController');
Route::resource('users', 'Tdt\Linda\Controllers\UserController');

// Auth
Route::get('login', 'Tdt\Linda\Controllers\AuthController@getLogin');
Route::post('login', 'Tdt\Linda\Controllers\AuthController@postLogin');
Route::get('logout', 'Tdt\Linda\Controllers\AuthController@getLogout');


// Dereferencing
Route::get('{id}', 'Tdt\Linda\Controllers\DatasetRefController@index')
->where('id', '[0-9]+');
