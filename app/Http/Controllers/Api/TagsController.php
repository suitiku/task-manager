<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tag;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Tag::with(['tasks'])->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tag = new Tag;
        $tag->fill($request->all())->save();
        return $tag;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Tag::with(['tasks'])->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);
        $tag->fill($request->all())->save();
        return $tag;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Tag::find($id)->delete();
    }
    
    // ユーザーIDで検索->使用数順にソート->色ごとにグルーピング
    public function getTagsByUserId(Request $request){
        $result;
        $tags = Tag::where('user_id',$request->user_id)
                ->with(['tasks'])
                ->withCount('tasks')
                ->orderBy('tasks_count','desc')
                ->get();
        foreach($tags as $tag){
            $result[$tag->color][] = $tag;
        }
        return $result;
    }
    
    //部分一致検索
    public function getTagsByKeyword(Request $request){
        $result;
        $tags = Tag::where('user_id',$request->user_id)
                ->where('name','like','%'.$request->keyword.'%')
                ->with(['tasks'])
                ->withCount('tasks')
                ->orderBy('tasks_count','desc')
                ->get();
        
        //検索結果がなかった場合
        if(count($tags) == 0){
            return [];
        }
        
        foreach($tags as $tag){
            $result[$tag->color][] = $tag;
        }
        return $result;
    }
}
