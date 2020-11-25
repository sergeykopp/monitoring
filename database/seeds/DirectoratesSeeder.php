<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Directorate;

class DirectoratesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Directorate::insert([
            ['name' => 'Волжская'],
            ['name' => 'Восточно-Сибирская'],
            ['name' => 'Дальневосточная'],
            ['name' => 'Западная'],
            ['name' => 'Западно-Сибирская'],
            ['name' => 'Московская'],
            ['name' => 'Сибирская'],
            ['name' => 'Уральская'],
            ['name' => 'Центральная'],
            ['name' => 'Южная'],
            ['name' => 'Иностранная'],
        ]);
    }
}
