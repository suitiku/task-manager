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
        $this->call(TagsTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(TasksTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
    }
}
