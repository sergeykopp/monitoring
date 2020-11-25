<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Cause;

class CausesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cause::insert([
            ['name' => 'Потеря услуг (плановое отключение электроэнергии)'],
            ['name' => 'Потеря услуг (внеплановое отключение электроэнергии)'],
            ['name' => 'Потеря услуг (проблемы на стороне провайдера)'],
            ['name' => 'Выход из строя электрооборудования банка'],
            ['name' => 'Выход из строя оборудования связи банка'],
            ['name' => 'Выход из строя иного оборудования'],
            ['name' => 'Сбои в работе оборудования'],
            ['name' => 'Сбои в работе ПО'],
        ]);
    }
}
