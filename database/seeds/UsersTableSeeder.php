<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initialUser = ['name' => 'suitiku','email' => 'suitiku@gmail.com','password' => Hash::make('pc2501'),'remember_token' => str_random(10)];
        App\User::create($initialUser);
        
        //テストユーザー
        // factory(App\User::class,20)->create();
    }
}
