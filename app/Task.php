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
    
    public function tags(){
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
    
    public function reminders(){
        return $this->hasMany('App\Reminder');
    }
    
    public function states(){
        return $this
                ->belongsToMany('App\State')
                ->withPivot('state_detail')
                ->orderBy('pivot_created_at','asc')
                ->withTimestamps();
    }
}
