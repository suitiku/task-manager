<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['user_id' => 1,'name' => '銀行対応','overview' => '金融機関','color' => 'red'], //1
            ['user_id' => 1,'name' => 'リース','overview' => '新規・再リース・買取など','color' => 'green'], //2
            ['user_id' => 1,'name' => '鶏割賦','overview' => '採卵鶏割賦購入取引','color' => 'orange'], //3
            ['user_id' => 1,'name' => '資料作成','overview' => '諸々','color' => 'yellow'], //4
            ['user_id' => 1,'name' => 'FCカード','overview' => 'FCカード処理','color' => 'black'], //5
            ['user_id' => 1,'name' => '日本政策金融公庫','overview' => '資料発送／回答など','color' => 'pink'], //6
        ];
        
        foreach($data as $d){
            App\Tag::create($d);
        }
    }
}
