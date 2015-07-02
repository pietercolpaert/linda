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

Route::resource('datasets', 'Tdt\Linda\Controllers\DatasetController');
Route::get('login', 'Tdt\Linda\Controllers\AuthController@getLogin');
Route::post('login', 'Tdt\Linda\Controllers\AuthController@postLogin');
Route::get('logout', 'Tdt\Linda\Controllers\AuthController@getLogout');

Route::get('{id}', 'Tdt\Linda\Controllers\DatasetRefController@index')
->where('id', '[0-9]+');

Route::get('/users/{id}', 'Tdt\Linda\Controllers\UserRefController@index')
->where('id', '[a-zA-Z0-9]+');
