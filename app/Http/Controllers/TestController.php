<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user(); //ログイン中のユーザー情報を取得
        //APIトークン処理
        $token = str_random(60);
        $user->forceFill(['api_token' => $token])->save();
        return view('test')->with(['user' => $user, 'token' =>$token]);
    }
}
