<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function tasksStates(){
        return $this->belongsToMany('App\Task','task_state_logs');
    }
}
