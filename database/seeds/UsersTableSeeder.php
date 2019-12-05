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
        $initialUser = ['name' => 'suitiku','email' => 'suitiku@gmail.com','password' => 'pc2501'];
        App\User::create($initialUser);
    }
}
