<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Service;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::insert([
            ['name' => 'Кондиционеры', 'id_group_services' => 1],
            ['name' => 'ЦОД - Инфраструктура', 'id_group_services' => 1],
            ['name' => 'FIS-Collection', 'id_group_services' => 2],
            ['name' => 'OpenWay', 'id_group_services' => 2],
            ['name' => 'Service Desk', 'id_group_services' => 2],
            ['name' => 'Афина', 'id_group_services' => 2],
            ['name' => 'ДБО', 'id_group_services' => 2],
            ['name' => 'Дилинг', 'id_group_services' => 2],
            ['name' => 'ИБСО', 'id_group_services' => 2],
            ['name' => 'Портал', 'id_group_services' => 2],
            ['name' => 'Рабис', 'id_group_services' => 2],
            ['name' => 'РБО', 'id_group_services' => 2],
            ['name' => 'Система Город', 'id_group_services' => 2],
            ['name' => 'ЦФТ', 'id_group_services' => 2],
            ['name' => 'Электронная почта', 'id_group_services' => 2],
            ['name' => 'CheckPoint', 'id_group_services' => 3],
            ['name' => 'Канал связи', 'id_group_services' => 3],
            ['name' => 'Телефония', 'id_group_services' => 3],
            ['name' => 'Электропитание', 'id_group_services' => 3],
            ['name' => 'Citrix', 'id_group_services' => 4],
            ['name' => 'Siebel', 'id_group_services' => 4],
            ['name' => 'VipNet Coordinator', 'id_group_services' => 4],
            ['name' => 'Базы данных', 'id_group_services' => 4],
            ['name' => 'Процессинг', 'id_group_services' => 4],
            ['name' => 'Серверы', 'id_group_services' => 4],
            ['name' => 'Шина', 'id_group_services' => 4],
        ]);
    }
}
