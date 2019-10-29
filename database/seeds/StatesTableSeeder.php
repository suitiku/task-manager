<?php

use Illuminate\Database\Seeder;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => '実行中','overview' => 'アクティブな状態'],
            ['name' => '対応待ち','overview' => '他者からの反応を待っている状態'],
            ['name' => '完了','overview' => 'タスクが完了した状態'],
            ['name' => 'タスク移動','overview' => '他の人に投げた状態'],
        ];
        
        foreach($data as $value){
            App\State::create($value);
        }
    }
}
