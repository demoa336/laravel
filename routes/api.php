<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::post('refresh', 'Api\AuthController@refresh');

Route::group(['middleware' => ['auth:api']], function () {

	Route::get('items', 'Api\ItemController@index');
	Route::post('items', 'Api\ItemController@store');
	Route::get('items/{id}', 'Api\ItemController@show');
	Route::patch('items', 'Api\ItemController@update');
	Route::delete('items', 'Api\ItemController@destroy');

	Route::post('logout', 'Api\AuthController@logout');

});
