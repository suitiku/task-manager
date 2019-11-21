<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function task(){
        return $this->belongsToMany('App\Task');
    }
}
