<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Trouble;

class TroublesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Trouble::insert([
            [
                'id_directorate' => null,
                'id_filial' => null,
                'id_city' => null,
                'id_office' => null,
                'id_source' => 22,
                'id_service' => 21,
                'id_status' => 3,
                'id_user' => 1,
                'started_at' => '2016-12-10 02:42:00',
                'finished_at' => '2016-12-10 04:12:00',
                'description' => 'MOS-SBL-BIP2 (Siebel БИН): Свободное место на диске C: меньше 5 Gb',
                'action' => 'Савин П. - поправит примерно через 2 часа.',
                'incident' => null,
            ],
            [
                'id_directorate' => 8,
                'id_filial' => 41,
                'id_city' => 113,
                'id_office' => 309,
                'id_source' => 21,
                'id_service' => 19,
                'id_status' => 2,
                'id_user' => 1,
                'started_at' => '2016-12-13 14:06:00',
                'finished_at' => '2016-12-13 16:37:00',
                'description' => 'ДО "Лысьвенский" perm-do-lisvensky-cr1',
                'action' => 'Гилев Д. - Сегодня в ОО Лысьвенский Нижегородского филиала прервалось электроснабжение, офис пока находится без связи.',
                'incident' => null,
            ],
            [
                'id_directorate' => null,
                'id_filial' => null,
                'id_city' => null,
                'id_office' => null,
                'id_source' => 20,
                'id_service' => 25,
                'id_status' => 3,
                'id_user' => 1,
                'started_at' => '2016-12-13 16:02:00',
                'finished_at' => '2016-12-13 16:53:00',
                'description' => 'NSK-TS45 (C:) System Disk free space test',
                'action' => 'Звонок Дубровских И.',
                'incident' => 13098,
            ],
            [
                'id_directorate' => 1,
                'id_filial' => 10,
                'id_city' => 21,
                'id_office' => null,
                'id_source' => 21,
                'id_service' => 19,
                'id_status' => 2,
                'id_user' => 1,
                'started_at' => '2016-12-14 14:25:00',
                'finished_at' => '2016-12-14 18:07:00',
                'description' => 'Ижевский izhevsk-main-cr1- No answer',
                'action' => 'Шишкина А.- плановое отключение до 17 часов местного времени.',
                'incident' => null,
            ],
            [
                'id_directorate' => null,
                'id_filial' => null,
                'id_city' => null,
                'id_office' => null,
                'id_source' => 17,
                'id_service' => 23,
                'id_status' => 3,
                'id_user' => 1,
                'started_at' => '2016-12-14 18:37:00',
                'finished_at' => '2016-12-14 18:38:00',
                'description' => 'EM Event: Critical:logminer_5 - The value of Database changes per day (Gb) is 151',
                'action' => 'Письмо АБД и Коржову К.',
                'incident' => null,
            ],
            [
                'id_directorate' => 7,
                'id_filial' => 37,
                'id_city' => 96,
                'id_office' => 279,
                'id_source' => 21,
                'id_service' => 19,
                'id_status' => 2,
                'id_user' => 1,
                'started_at' => '2016-12-19 08:59:00',
                'finished_at' => '2016-12-19 09:24:00',
                'description' => 'ДО Рубцовский barnaul-rubcovsk2-cr1',
                'action' => 'Иванов В.: нет электропитания. Сроков нет.',
                'incident' => null,
            ],
            [
                'id_directorate' => null,
                'id_filial' => null,
                'id_city' => null,
                'id_office' => null,
                'id_source' => 18,
                'id_service' => 25,
                'id_status' => 3,
                'id_user' => 1,
                'started_at' => '2016-12-23 22:18:00',
                'finished_at' => '2016-12-23 22:19:00',
                'description' => 'Мало места на диске! Сервер: MSK-SPS10-02.corp.icba.biz',
                'action' => 'SLA=2. Звонок Кротевичу К.- некритично.',
                'incident' => 13146,
            ],
            /*[
                'id_directorate' => null,
                'id_filial' => null,
                'id_city' => null,
                'id_office' => null,
                'id_source' => 1,
                'id_service' => 1,
                'id_status' => 1,
                'id_user' => 1,
                'started_at' => '',
                'finished_at' => '',
                'description' => '',
                'action' => '',
                'incident' => null,
            ],*/
        ]);
    }
}
