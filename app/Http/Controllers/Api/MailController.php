<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public function sendMail(){
        \Mail::raw('hogehoge', function($message) {
            $message->to('suitiku@gmail.com')->subject('test');
        });
    }
}
