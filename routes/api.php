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



    Route::post('register', 'Api\UserApiController@register')->name('register');
    Route::post('authenticate', 'Api\UserApiController@authenticate')->name('authenticate');

    
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('list-users', 'Api\UserApiController@listUser')->name('list-users');
        Route::post('updated', 'Api\UserApiController@updated')->name('updated');
        Route::post('delete', 'Api\UserApiController@deleteUser')->name('delete');

});
