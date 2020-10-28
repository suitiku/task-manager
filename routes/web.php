<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    if(Auth::user()){
        return redirect('/home');
    }
    return view('welcome');
});

//ホーム画面（認証必須）
Route::get('/home','HomeController@index');


//テストコンポーネント用test.blade.php
Route::get('/test','TestController@index');

// メールプレビュー
Route::get('mailable/preview',function(){
    return new App\Mail\SampleNotification();    
});

// メール送信
Route::get('mailable/send','MailController@sampleNotification');
