<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => ['cors', 'json.response']], function () {
    // ...

    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','Auth\ApiAuthController@register')->name('register.api');
    Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');

    Route::post('/createassignment','AssignmentController@createassignment')->middleware('auth:api');//->middleware('api.superAdmin');

    Route::post('/markassignmentandcomment','AssignmentController@markassignmentandcomment')->middleware('auth:api');//->middleware('api.superAdmin');

    Route::post('/submitassignment','AssignmentController@submitassignment')->middleware('auth:api');//->middleware('api.superAdmin');



    // ...
});

Route::get('/autherror', 'Auth\ApiAuthController@authenticationerror')->name('autherror');


Route::group(['middleware' => ['auth:api', 'user_accessible']], function () {

});


