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

Route::get('/', 'Tdt\Dapps\Controllers\HomeController@index');

Route::resource('datasets', 'Tdt\Dapps\Controllers\DatasetController');
Route::get('login', 'Tdt\Dapps\Controllers\AuthController@getLogin');
Route::post('login', 'Tdt\Dapps\Controllers\AuthController@postLogin');
Route::get('logout', 'Tdt\Dapps\Controllers\AuthController@getLogout');

Route::get('{id}', 'Tdt\Dapps\Controllers\DerefController@index')
->where('id', '[0-9]+');
