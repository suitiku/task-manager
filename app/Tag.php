<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function tasks(){
        return $this->belongsToMany('App\Task');
    }
    
    public function myLists(){
        return $this->belongsToMany('App\MyList');
    }
}
