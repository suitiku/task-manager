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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

//ホーム画面（タスク一覧、
Route::get('/home',function(){
    return view('home');
});


//テストコンポーネント用test.blade.php
Route::get('/test',function(){
    return view('test');
});
