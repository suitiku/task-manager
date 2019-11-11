<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function tasks(){
        return $this->hasMany('App\Task');
    }
}
