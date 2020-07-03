<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function task(){
        return $this->belongsTo('App\Task');
    }
}
