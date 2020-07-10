<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Task;
use App\Item;
use App\Project;
use App\Reminder;
use Illuminate\Support\Facades\DB;

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
                        'tags',
                        'reminders'
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
        $task = DB::transaction(function() use($request){
            $task = new Task;
            $task->fill($request->all())->save();
        
            //状態を「実行中」で登録
            $task->states()->attach(1,['state_detail' => 'タスクが作成されました。']);
            $task = $task->with(['project','items','states','tags','reminders'])->find($task->id);
            return $task;
        });
        
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
        return Task::with(['project','items','states','tags','reminders'])->find($id);
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
        $result = DB::transaction(function() use($task,$request) {
            $task->states()->attach($request->state_id,['state_detail' => $request->state_detail]);
            // 変化先状態が完了／未完了の場合はReminderを削除する
            if(($request->state_id == 2 || $request->state_id == 5) && $task->reminders){
                foreach($task->reminders as $index=>$reminder){
                    $reminderIds[] = $reminder->id;
                }
                Reminder::destroy($reminderIds);
            }
        });
        
    }
    
    // ユーザーIDで検索／本日0:00からのタスクのみ／プロジェクト無しのみ
    public function getCurrentTasksByUserId(Request $request){
        $result;
        $defaultProjectId = Project::where('user_id',$request->user_id)->first()->id;
        $validDatetime = strtotime(date('Y-m-d'));
        $tasks = Task::where('user_id',$request->user_id)
                    ->where('project_id',$defaultProjectId)
                    ->with(['project','states','items','tags','reminders'])
                    ->get();
        foreach($tasks as $index => $task){
            $statesCount = count($task->states);
            if(($task->states[$statesCount -1]->id != 2 && $task->states[$statesCount -1]->id != 5) || strtotime($task->states[$statesCount -1]->pivot->created_at) >= $validDatetime){
                $result[] = $task;
            }
        }
        return $result;
    }
    
    //ユーザーIDで過去のものも含めた全タスク取得
    public function getAllTasksByUserId(Request $request){
        return Task::where('user_id',$request->user_id)
                    ->with(['project','items','tags','states','reminders'])
                    ->get();
    }
    
    // プロジェクトIDでフィルター
    public function getTasksByProjectIds(Request $request){
        return Task::where('user_id',$request->user_id)
                ->whereIn('project_id',$request->project_ids)
                ->with(['project','items','tags','states','reminders'])
                ->get();
    }
    
    //コピー
    public function copy($id){
        //コピー元タスクを取得
        $originalTask = Task::with(['project','items','states','tags'])->find($id);
        
        //分解
        //task本体
        $task = [
            'user_id' => $originalTask['user_id'],
            'project_id' => $originalTask['project_id'],
            'name' => $originalTask['name'],
            'overview' => $originalTask['overview'],
            'priority' => $originalTask['priority'],
            'difficulty' => $originalTask['difficulty'],
            'start_date' => $originalTask['start_date'],
            'dead_line' => $originalTask['dead_line'],
            'is_template' => $originalTask['is_template']
        ];
        
        //items
        $items = [];
        foreach($originalTask['items'] as $item){
            $items[] = ['name' => $item->name,'is_checked' => $item->is_checked];
        }
        
        //tags
        $tags = [];
        foreach($originalTask->tags as $tag){
            $tags[] = $tag->id;
        }
        
        //トランザクション
        $newTask = DB::transaction(function() use($task,$items,$tags){
            //コピー先タスクインスタンスを作成
            $newTask = new Task;
            
            //本体をコピー
            $newTask->fill($task)->save();
            
            //状態を設定
            $newTask->states()->attach(1,['state_detail' => '作成']);
            
            //items
            foreach($items as $item){
                $item['task_id'] = $newTask['id'];
                $itemInstance = new Item;
                $itemInstance->fill($item)->save();
            }
            
            //tags
            $newTask->tags()->sync($tags);
            
            return $newTask;
        });
        return Task::with(['project','items','states','tags','reminders'])->find($newTask['id']);
    }
    
    public function createTaskFromTemplate($id){
        //テンプレート元タスクを取得
        $originalTask = Task::with(['project','items','states','tags'])->find($id);
        
        //現在時刻を取得
        $currentDatetime = date('Y-m-d H:i:s',time());
        
        //締め切り時間を設定（デフォルトは開始から8時間後）
        $deadLine = date('Y-m-d H:i:s', strtotime($currentDatetime) + 28800);
        
        //分解
        //task本体
        $task = [
            'user_id' => $originalTask['user_id'],
            'project_id' => $originalTask['project_id'],
            'name' => $originalTask['name'],
            'overview' => $originalTask['overview'],
            'priority' => $originalTask['priority'],
            'difficulty' => $originalTask['difficulty'],
            'start_date' => $currentDatetime,
            'dead_line' => $deadLine,
            'is_template' => false
        ];
        
        //items
        $items = [];
        foreach($originalTask['items'] as $item){
            $items[] = ['name' => $item->name,'is_checked' => false]; //アイテムのチェックはリセット
        }
        
        //tags
        $tags = [];
        foreach($originalTask->tags as $tag){
            $tags[] = $tag->id;
        }
        
        //トランザクション
        $newTask = DB::transaction(function() use($task,$items,$tags){
            //出力先タスクインスタンスを作成
            $newTask = new Task;
            
            //本体をコピー
            $newTask->fill($task)->save();
            
            //状態を設定
            $newTask->states()->attach(1,['state_detail' => '作成']);
            
            //items
            foreach($items as $item){
                $item['task_id'] = $newTask['id'];
                $itemInstance = new Item;
                $itemInstance->fill($item)->save();
            }
            
            //tags
            $newTask->tags()->sync($tags);
            
            return $newTask;
        });
        return Task::with(['project','items','states','tags','reminders'])->find($newTask['id']);
    }
    
    //集計
    public function aggregate(Request $request){
        $result = ['byState' => [],'byDate' => []];
        $tasks = Task::where('user_id',$request->user_id)
                    ->with(['project','items','states','tags'])
                    ->get();
        foreach($tasks as $task){
            $statesCount = count($task->states);
            $lastState = $task->states[$statesCount -1];
            //状態別
            if(array_key_exists($lastState->name,$result['byState'])){
                $result['byState'][$lastState->name] += 1;
            }else{
                $result['byState'][$lastState->name] = 1;
            }
            //完了済み日付別
            if($lastState->id == 2){
                $date = substr($lastState->pivot->created_at,0,10);
                if(array_key_exists($date,$result['byDate'])){
                    $result['byDate'][$date] += 1;
                }else{
                    $result['byDate'][$date] = 1;
                }
            }
            
        }
        //日付昇順でソート
        ksort($result['byDate']);
        return $result;
    }
}
