<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TableInfoController extends Controller
{
    public function getTableInfo($table){
        $result = DB::table('information_schema.columns')->where('TABLE_NAME',$table)->get();
        return $result;
    }
}
