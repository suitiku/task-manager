<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function tasks(){
        return $this->belongsTo('App\Task');
    }
}
