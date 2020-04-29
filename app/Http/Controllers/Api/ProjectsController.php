<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Project;
use App\Task;
use App\Http\Controllers\Api\TasksController;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Project::with(['tasks.states','tasks.items','tasks.tags'])->get();
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
        $project = new Project;
        $project->fill($request->all())->save();
        return $project;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Project::with(['tasks.states','tasks.items','tasks.tags'])->find($id);
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
        $project = Project::find($id);
        $project->fill($request->all())->save();
        return $project;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Project::destroy($id);
    }
    
    // ユーザーIDで検索
    public function getProjectsByUserId(Request $request){
        return Project::where('user_id',$request->user_id)->with(['tasks.states','tasks.items','tasks.tags'])->get();
    }
    
    public function copy($id){
        //元のprojectを取得
        $originalProject = Project::with(['tasks.states','tasks.items','tasks.tags'])->find($id);
        
        //プロジェクト本体
        $project = [
            'user_id' => $originalProject['user_id'],
            'name' => $originalProject['name'],
            'overview' => $originalProject['overview'],
            'dead_line' => $originalProject['dead_line']
        ];
        
        //tasks
        $taskIds = [];
        foreach($originalProject->tasks as $task){
            $taskIds[] = $task->id;
        }
        
        $newProject = DB::transaction(function() use($project,$taskIds){
            //コピー先のProjectインスタンスを作成
            $newProject = new Project;
            //TasksControllerインスタンスを作成
            $task = new TasksController;
            
            //コピー
            $newProject->fill($project)->save();
            $projectIdPostObject = ['project_id' => $newProject->id];
            
            foreach($taskIds as $taskId){
                //タスクをコピー
                $newTask = $task->copy($taskId);
                //コピーされたタスクのproject_idを更新
                $newTask->update($projectIdPostObject);
            }
            
            return $newProject;
        });
        
        return Project::with(['tasks.states','tasks.items','tasks.tags'])->find($newProject->id);
    }
}
