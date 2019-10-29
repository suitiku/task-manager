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
            ['name' => 'メール処理','overview' => ' ','priority' => 1,'difficulty' => 1,'dead_line' => '2022-05-01 19:00:00'], //1    
            ['name' => '借入書類処理（北陸）','overview' => ' ','priority' => 3,'difficulty' => 3,'dead_line' => '2022-05-01 20:00:00'], //2 
            ['name' => '稟議書作成（富山銀行借入）','overview' => '条件：5億円、1.1%、5年、毎月返済、担保保証なし','priority' => 3,'difficulty' => 2,'dead_line' => '2022-05-02 14:00:00'], //3 
            ['name' => '持ち物リスト','overview' => '','priority' => 3,'difficulty' => 1,'dead_line' => '2022-05-03 18:00:00'], //4 
        ];
        
        foreach($data as $value){
            $data = App\Task::create($value);
            App\Task::find($data->id)->statesTasks()->attach(1);
        }
    }
}
