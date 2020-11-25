<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            ['name' => 'Administrator'],
            ['name' => 'Dutyman'],
            ['name' => 'Risk'],
        ]);
    }
}
