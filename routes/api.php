<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
Route::resource('states','Api\StatesController');
Route::resource('items','Api\ItemsController');
Route::resource('tags','Api\TagsController');
Route::resource('projects','Api\ProjectsController');

//タグ
Route::post('tag_task','Api\TasksController@attachTags');
Route::delete('tag_task','Api\TasksController@detachTags');
Route::put('tag_task','Api\TasksController@syncTags');

//状態（state）
Route::post('state_task','Api\TasksController@changeState');

//ユーザー別
Route::get('mytasks','Api\TasksController@getTasksByUserId');
Route::get('mytags','Api\TagsController@getTagsByUserId');

//メール送信テスト
Route::get('mail/send','Api\MailController@sendMail');
