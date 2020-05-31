<?php

use Illuminate\Database\Seeder;

class ApiSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\ApiSettings::class ,1)-> create();
    }
}
