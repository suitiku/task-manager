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
            // ['name' => '作成','overview' => '作成された直後'], ログで記録
            ['name' => '実行中','overview' => 'アクティブな状態'], //1
            ['name' => '対応待ち','overview' => '他者からの反応を待っている状態'], //2
            ['name' => '完了','overview' => 'タスクが完了した状態'], //3
            ['name' => 'タスク移動','overview' => '他の人に投げた状態'], //4
            ['name' => '未完了','overview' => '完了できずに終了'], //5
            // ['name' => '編集','overview' => 'タスクの内容を変更'], 編集はログで記録する
        ];
        
        foreach($data as $value){
            App\State::create($value);
        }
    }
}
