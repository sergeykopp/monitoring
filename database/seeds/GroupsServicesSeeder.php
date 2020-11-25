<?php

use Illuminate\Database\Seeder;
use Kopp\Models\GroupServices;

class GroupsServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       GroupServices::insert([
            ['name' => 'Инфраструктурные'],
            ['name' => 'Прикладные'],
            ['name' => 'Связь'],
            ['name' => 'Системные'],
        ]);
    }
}
