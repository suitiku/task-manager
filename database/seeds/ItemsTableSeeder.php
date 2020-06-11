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
            ['task_id' => 4,'name' => '金銭消費貸借契約書','is_checked' => 0], //1  
            ['task_id' => 4,'name' => '金銭消費貸借契約書（予備）','is_checked' => 0], //2
            ['task_id' => 4,'name' => '印鑑証明書','is_checked' => 0], //3
            ['task_id' => 4,'name' => 'クリアファイル','is_checked' => 0], //4
            ['task_id' => 4,'name' => '朱肉','is_checked' => 0], //5
            ['task_id' => 4,'name' => '押印シート','is_checked' => 0], //6
        ];
        
        foreach($data as $value){
            App\Item::create($value);
        }
    }
}
