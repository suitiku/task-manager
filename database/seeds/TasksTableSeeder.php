<?php

use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['project_id' => 1,'state_id' => 1,'name' => 'メール処理','overview' => ' ','priority' => 1,'difficulty' => 1,'start_date' => '2019-11-10 12:00:00','dead_line' => '2022-05-01 19:00:00'], //1    
            ['project_id' => 2,'state_id' => 1,'name' => '借入書類処理（北陸）','overview' => ' ','priority' => 3,'difficulty' => 3,'start_date' => '2019-11-30 15:00:00','dead_line' => '2022-05-01 20:00:00'], //2 
            ['project_id' => 2,'state_id' => 1,'name' => '稟議書作成（富山銀行借入）','overview' => '条件：5億円、1.1%、5年、毎月返済、担保保証なし','priority' => 3,'difficulty' => 2,'start_date' => '2019-12-10 10:00:00','dead_line' => '2022-05-02 14:00:00'], //3 
            ['project_id' => 1,'state_id' => 1,'name' => '持ち物リスト','overview' => '','priority' => 3,'difficulty' => 1,'start_date' => '2019-12-20 18:30:00','dead_line' => '2022-05-03 18:00:00'], //4 
        ];
        
        $taglist = [2,4,5,1];
        
        foreach($data as $index => $value){
            $task = App\Task::create($value);
            $task->tags()->attach($taglist[$index]);
        }
    }
}
