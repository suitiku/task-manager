<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

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

// ログイン用
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//apiログイン
Route::post('/login',function(Request $request){
    $credentials = $request->validate([
       'email' => 'required|email',
       'password' => 'required',
    ]);
    if(auth()->attempt($credentials)){
        return true;
    }else{
        return response(['message' => 'ユーザーが見つかりません'],422);
    }
});

// APIトークン発行
// Route::post('/airlock/token', function(Request $request){

// });
    


//認証後にリクエスト可能
// Route::middleware('auth:sanctum')->group(function(){
    
    // sanctum認証テスト用
    Route::get('test',function(){
        return 'test';
    });
    
    // タグ検索
    Route::get('tags/search','Api\TagsController@getTagsByKeyword');
    
    //フィルター
    Route::get('tasks/projects','Api\TasksController@getTasksByProjectIds');
    
    //テーブル情報取得
    Route::get('table_info/{table}','Api\TableInfoController@getTableInfo');
    
    //各マスターテーブル
    Route::resource('tasks','Api\TasksController');
    Route::resource('states','Api\StatesController');
    Route::resource('items','Api\ItemsController');
    Route::resource('tags','Api\TagsController');
    Route::resource('projects','Api\ProjectsController');
    Route::resource('reminders','Api\RemindersController');
    
    //タグ
    Route::post('tag_task','Api\TasksController@attachTags');
    Route::delete('tag_task','Api\TasksController@detachTags');
    Route::put('tag_task','Api\TasksController@syncTags');
    
    //状態（state）
    Route::post('state_task','Api\TasksController@changeState');
    // スター
    Route::put('star/{id}','Api\TasksController@changeStared');
    
    //ユーザー別
    Route::get('myprojects','Api\ProjectsController@getProjectsByUserId');
    Route::get('mytasks','Api\TasksController@getAllTasksByUserId');
    Route::get('mytasks/current','Api\TasksController@getCurrentTasksByUserId');
    Route::get('mytags','Api\TagsController@getTagsByUserId');
    Route::get('myreminders','Api\RemindersController@getRemindersByUserId');
    
    //メール送信テスト
    Route::get('mail/send','Api\MailController@sendMail');
    
    //コピー
    Route::post('tasks/copy/{id}','Api\TasksController@copy');
    Route::post('projects/copy/{id}','Api\ProjectsController@copy');
    
    //テンプレート
    Route::post('tasks/template/{id}','Api\TasksController@createTaskFromTemplate');
    
    //集計系
    Route::get('mytasks/aggregate','Api\TasksController@aggregate');
    

// });
