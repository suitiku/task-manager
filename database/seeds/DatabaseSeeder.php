<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        
        // 本番環境では以下は無効
        // $this->call(TagsTableSeeder::class);
        // $this->call(ProjectsTableSeeder::class);
        // $this->call(TasksTableSeeder::class);
        // $this->call(ItemsTableSeeder::class);
    }
}
