<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function items(){
        return $this->hasMany('App\Item');
    }
    
    public function statesTasks(){
        return $this->belongsToMany('App\State','state_task')->withTimestamps();
    }
    
}
