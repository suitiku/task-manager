<?php

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['task_id' => 4,'name' => '金銭消費貸借契約書','is_checked' => 0,'memo' => '実印、ゴム版を押印'], //1  
            ['task_id' => 4,'name' => '金銭消費貸借契約書（予備）','is_checked' => 0,'memo' => ''], //2
            ['task_id' => 4,'name' => '印鑑証明書','is_checked' => 0,'memo' => '日付を確認すること'], //3
            ['task_id' => 4,'name' => 'クリアファイル','is_checked' => 0,'memo' => ''], //4
            ['task_id' => 4,'name' => '朱肉','is_checked' => 0,'memo' => ''], //5
            ['task_id' => 4,'name' => '押印シート','is_checked' => 0,'memo' => ''], //6
        ];
        
        foreach($data as $value){
            App\Item::create($value);
        }
    }
}
