<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function statesTasks(){
        return $this->belongsToMany('App\Task','state_task');
    }
}
