<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('ApiSettingsSeeder');
        $this->call('UsersSeeder');
        $this->call('PostsSeeder');
        $this->call('CommentsSeeder');
    }
}
