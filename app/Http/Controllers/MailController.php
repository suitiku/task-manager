<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleNotification;

class MailController extends Controller {
    
    public function sampleNotification(){
        $text = 'this is test mail';
        $to = 'suitiku@gmail.com';
        Mail::to($to)->send(new SampleNotification($text));
    }
    
}