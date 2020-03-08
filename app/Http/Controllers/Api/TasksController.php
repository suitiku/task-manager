<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Task::with([
                        'project',
                        'items',
                        'states',
                        'tags'
                    ])->get();
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
        $task = new Task;
        $task->fill($request->all())->save();
        return $task;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Task::with([
                        'project',
                        'items',
                        'states',
                        'tags'
                    ])->find($id);
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
        $task = Task::find($id);
        $task->fill($request->all())->save();
        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 真偽値で返ってくる
        return Task::destroy($id);
    }
    
    //タグ設定関連
    public function attachTags(Request $request){
        $task = Task::find($request->task_id);
        $task->tags()->attach($request->tag_id);
        $data = array('task_id' => $request->task_id, 'tag_id' => $request->tag_id);
        $result = array('status' => 'success','data' => $data);
        return $result;
    }
    
    public function detachTags(Request $request){
        $task = Task::find($request->task_id);
        $task->tags()->detach($request->tag_id);
        $data = array('task_id' => $request->task_id, 'tag_id' => $request->tag_id);
        $result = array('status' => 'success','data' => $data);
        return $result;
    }
    
    //タグを同期する場合（メソッドはput）
    public function syncTags(Request $request){
        $task = Task::find($request->task_id);
        $task->tags()->sync($request->tag_ids);
        $data = array('task_id' => $request->task_id, 'tag_ids' => $request->tag_ids);
        $result = array('status' => 'success','data' => $data);
        return $result;
    }
    
    //状態管理関連
    //変更
    public function changeState(Request $request){
        $task = Task::find($request->task_id);
        $task->states()->attach($request->state_id,['state_detail' => $request->detail]);
    }
    
    // ユーザーIDで検索
    public function getTasksByUserId(Request $request){
        return Task::where('user_id',$request->user_id)->with(['project','items','states','tags'])->get();
    }
}
