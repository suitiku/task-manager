<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MyList extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function myListItems(){
        return $this->hasMany('App\MyListItem');
    }
    
    public function tags(){
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    public function user(){
        return $this->belongsTo('App\User');
    }    
}
