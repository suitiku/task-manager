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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//テーブル情報取得
Route::get('table_info/{table}','Api\TableInfoController@getTableInfo');

//各マスターテーブル
Route::resource('tasks','Api\TasksController');
Route::post('state_task','Api\TasksController@insertStateTask');
Route::resource('states','Api\StatesController');
Route::resource('items','Api\ItemsController');
