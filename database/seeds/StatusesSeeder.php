<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Status;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::insert([
            ['name' => 'Чрезвычайный'],
            ['name' => 'Высокий'],
            ['name' => 'Средний'],
            ['name' => 'Предупреждение'],
            ['name' => 'Информация'],
            ['name' => 'Не классифицирован'],
        ]);
    }
}
