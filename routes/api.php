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


Route::group(['middleware' => ['CORS']], function () {
    Route::post('register', 'UserController@register');
    Route::post('login', 'UserController@authenticate');
    Route::get('open', 'DataController@open');
    Route::post('user/weather', 'UserController@userLocationData');
    Route::get('data', 'StationController@getCurrentData');

    Route::post('station/login', 'StationController@authenticate');
    Route::post('station/send', 'StationController@sendData');
    Route::get('station/list', 'StationController@getListOfStations');

    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('user', 'UserController@getAuthenticatedUser');
        Route::get('closed', 'DataController@closed');
    });
});

