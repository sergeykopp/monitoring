<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Filial;

class FilialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Filial::insert([
            ['name' => 'Башкортостанский', 'id_directorate' => 1],
            ['name' => 'Марий Элский', 'id_directorate' => 1],
            ['name' => 'Мордовский', 'id_directorate' => 1],
            ['name' => 'Нижегородский', 'id_directorate' => 1],
            ['name' => 'Оренбургский', 'id_directorate' => 1],
            ['name' => 'Пензенский', 'id_directorate' => 1],
            ['name' => 'Самарский', 'id_directorate' => 1],
            ['name' => 'Саратовский', 'id_directorate' => 1],
            ['name' => 'Татарстанский', 'id_directorate' => 1],
            ['name' => 'Удмуртский', 'id_directorate' => 1],
            ['name' => 'Ульяновский', 'id_directorate' => 1],
            ['name' => 'Чувашский', 'id_directorate' => 1],
            ['name' => 'Иркутский', 'id_directorate' => 2],
            ['name' => 'Красноярский', 'id_directorate' => 2],
            ['name' => 'Хакасский', 'id_directorate' => 2],
            ['name' => 'Амурский', 'id_directorate' => 3],
            ['name' => 'Бурятский', 'id_directorate' => 3],
            ['name' => 'Забайкальский', 'id_directorate' => 3],
            ['name' => 'Приморский', 'id_directorate' => 3],
            ['name' => 'Сахайский', 'id_directorate' => 3],
            ['name' => 'Хабаровский', 'id_directorate' => 3],
            ['name' => 'Архангельский', 'id_directorate' => 4],
            ['name' => 'Вологодский', 'id_directorate' => 4],
            ['name' => 'Калининградский', 'id_directorate' => 4],
            ['name' => 'Карельский', 'id_directorate' => 4],
            ['name' => 'Комийский', 'id_directorate' => 4],
            ['name' => 'Ленинградский', 'id_directorate' => 4],
            ['name' => 'Мурманский', 'id_directorate' => 4],
            ['name' => 'Новгородский', 'id_directorate' => 4],
            ['name' => 'Псковский', 'id_directorate' => 4],
            ['name' => 'Санкт-Петербургский', 'id_directorate' => 4],
            ['name' => 'Новосибирский', 'id_directorate' => 5],
            ['name' => 'Омский', 'id_directorate' => 5],
            ['name' => 'Тюменский', 'id_directorate' => 5],
            ['name' => 'Ханты-Мансийский', 'id_directorate' => 5],
            ['name' => 'Московский', 'id_directorate' => 6],
            ['name' => 'Алтайский', 'id_directorate' => 7],
            ['name' => 'Кемеровский', 'id_directorate' => 7],
            ['name' => 'Томский', 'id_directorate' => 7],
            ['name' => 'Курганский', 'id_directorate' => 8],
            ['name' => 'Пермский', 'id_directorate' => 8],
            ['name' => 'Свердловский', 'id_directorate' => 8],
            ['name' => 'Челябинский', 'id_directorate' => 8],
            ['name' => 'Белгородский', 'id_directorate' => 9],
            ['name' => 'Брянский', 'id_directorate' => 9],
            ['name' => 'Владимирский', 'id_directorate' => 9],
            ['name' => 'Воронежский', 'id_directorate' => 9],
            ['name' => 'Ивановский', 'id_directorate' => 9],
            ['name' => 'Калужский', 'id_directorate' => 9],
            ['name' => 'Костромской', 'id_directorate' => 9],
            ['name' => 'Курский', 'id_directorate' => 9],
            ['name' => 'Липецкий', 'id_directorate' => 9],
            ['name' => 'Орловский', 'id_directorate' => 9],
            ['name' => 'Рязанский', 'id_directorate' => 9],
            ['name' => 'Смоленский', 'id_directorate' => 9],
            ['name' => 'Тверской', 'id_directorate' => 9],
            ['name' => 'Тульский', 'id_directorate' => 9],
            ['name' => 'Ярославский', 'id_directorate' => 9],
            ['name' => 'Астраханский', 'id_directorate' => 10],
            ['name' => 'Волгоградский', 'id_directorate' => 10],
            ['name' => 'Краснодарский', 'id_directorate' => 10],
            ['name' => 'Ростовский', 'id_directorate' => 10],
            ['name' => 'Ставропольский', 'id_directorate' => 10],
            ['name' => 'Афинский', 'id_directorate' => 11],
            ['name' => 'Лондонский', 'id_directorate' => 11],
        ]);
    }
}
