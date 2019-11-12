<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => '単体タスク','overview' => '単体のタスク、特殊ID','dead_line' => NULL],
            ['name' => 'プロジェクト1','overview' => 'テスト','dead_line' => '2024-02-23 01:00:00'],
            ['name' => 'プロジェクト2','overview' => 'タスクなしの場合のテスト','dead_line' => '2024-02-25 02:00:00'],
        ];
        
        foreach($data as $value){
            App\Project::create($value);
        }
    }
}
