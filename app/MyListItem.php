<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MyListItem extends Model
{
    protected $guarded = array('id','created_at','updated_at');
    
    public function myList(){
        return $this->belongsTo('App\MyList');
    }
}
