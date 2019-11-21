<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function items(){
        return $this->hasMany('App\Item');
    }
    
    public function project(){
        return $this->belongsTo('App\Project');
    }
    
    public function state(){
        return $this->belongsTo('App\State');
    }
    
    public function tags(){
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
}
