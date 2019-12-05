<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public function sendMail(){
        $message = 'hogehoge';
        $result = mail('suitiku@gmail.com','testmail',$message);
    }
}
