<?php

use Illuminate\Database\Seeder;
use Kopp\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'login' => 'koppsv',
                'name' => 'Копп Сергей Владимирович',
                'email' => 'kopp2@binbank.ru',
                'password' => '$2y$10$lA970x6CE9PD/oQ6M6zULOFTk5HJKQnUXIJ7NAYFK5/8luT7HXXfW',
                'remember_token' => 'u6ce6rQNTSXh2Npf1kYmxiqWmwe8iUVRggSmmKEqodHCxO3wi9TmtirjVyvf',
                'created_at' => '2017-01-02 13:49:39',
                'updated_at' => '2017-01-02 14:35:28',
            ],
            [
                'login' => 'vkraynev',
                'name' => 'Крайнев Владимир Алексеевич',
                'email' => 'vkraynev@binbank.ru',
                'password' => '$2y$10$5/Tgd.uLFvEs6xBLagUfwujemJ0p5atmWioi8.02QABUm5KTWpfOG',
                'remember_token' => 'd0tNF687oWXX8m9gl0q7hfnZ6Vn52dxG4VAhZqGrYegApGIFn2wZeMl3D5BT',
                'created_at' => '2017-01-02 13:49:39',
                'updated_at' => '2017-01-06 12:07:09',
            ],
            [
                'login' => 'yboldyrev',
                'name' => 'Болдырев Юрий Николаевич',
                'email' => 'boldyrev@binbank.ru',
                'password' => '$2y$10$7x.Jjs4q9mW.m9ZwcT1vMusKdWGGYgSMlyEPzwtGfX/CLhZZvoDRa',
                'remember_token' => 'y5Pc45NSERsILcj1Fn6yeAGOp3Wfd7QIkqFEr86rkaQpTjDpH1J9asLQz4Ud',
                'created_at' => '2017-01-02 13:49:39',
                'updated_at' => '2017-01-06 12:09:24',
            ],
            [
                'login' => 'moiseenko',
                'name' => 'Моисеенко Виталий Сергеевич',
                'email' => 'moiseenko@binbank.ru',
                'password' => '$2y$10$L3uJyAoHJysdBIKhR7Sou.UtZIQLLOsGoJNe0u6tnEPW5finBGwGK',
                'remember_token' => 'RJNXqCqKyQkQKHgWG2tR3qFYEZD54aFxnGVOLYWkArEVpC2ZdJYiSPdTa47W',
                'created_at' => '2017-01-06 15:37:04',
                'updated_at' => '2017-01-06 15:38:59',
            ],
            [
                'login' => 'matveytchuk',
                'name' => 'Матвейчук Сергей Александрович',
                'email' => 'matveytchuk@binbank.ru',
                'password' => '$2y$10$s1l73LSaFylPDcEv.rG6juMtwFe9dzW6qMVeBL3YqmbyhGdls1FlG',
                'remember_token' => 'd30bLgYH1erABZvf2ly04pciNh0SMKdaVy1wbAYy6i8bAYTCvxZBClO8JKZW',
                'created_at' => '2017-01-06 15:40:03',
                'updated_at' => '2017-01-06 15:48:49',
            ],
            [
                'login' => 'AANikiforov',
                'name' => 'Никифоров Алексей Александрович',
                'email' => 'AANikiforov@binbank.ru',
                'password' => '$2y$10$fRs8OdxNBUDew5F253isbOh0mCw7pcfysf1ov4ikqDlSgfiP994ra',
                'remember_token' => '41KLIiN6folz1IsWetOm8RkESa01ay4O6z9hSbQQFoVYnsmHnZwumnZThmaL',
                'created_at' => '2017-01-06 15:41:00',
                'updated_at' => '2017-01-06 15:49:56',
            ],
            [
                'login' => 'Gurev',
                'name' => 'Гурьев Евгений Васильевич',
                'email' => 'Gurev@binbank.ru',
                'password' => '$2y$10$jmFtzpPRYZoCP9JBxdVhHug.TMgffBtk/KmIv3OGN0Y6epiIoD2UG',
                'remember_token' => 'bMd7VzbLUV6NEeZD7d6p5A1F9zPg2aqIvwbTSI95BZibENMHeA7zFULttIpi',
                'created_at' => '2017-01-06 15:41:40',
                'updated_at' => '2017-01-06 15:50:56',
            ],
            [
                'login' => 'PFesenko',
                'name' => 'Фесенко Павел Геннадьевич',
                'email' => 'PFesenko@binbank.ru',
                'password' => '$2y$10$g7mrnq.ej90WYko1fwsNd./CGLWv8U3CvX4aPyGgO4XfK4yXWN6M.',
                'remember_token' => 'Bw1K5d7dy9vytKvesp7jia8jfnXaTY6StdbMHHe5n4p6QISq4PfDsqi5scJl',
                'created_at' => '2017-01-06 15:42:22',
                'updated_at' => '2017-01-06 15:51:55',
            ],
            [
                'login' => 'kokhtorov',
                'name' => 'Кохторов Роман Валериевич',
                'email' => 'kokhtorov@binbank.ru',
                'password' => '$2y$10$VHok9hjTJQcUWRHRHidp0euBk/6PhAJYfypGc9A7lgxFzdI.uiQfq',
                'remember_token' => 'MnAthU2RBf2bm6ehoTxVur63eTz69JnwuqjU03WdxoJivHIU3EnqKHqNzSeL',
                'created_at' => '2017-01-06 15:43:02',
                'updated_at' => '2017-01-06 15:53:04',
            ],
            [
                'login' => 'yu.vishnya',
                'name' => 'Вишня Юрий Андреевич',
                'email' => 'yu.vishnya@BINBank.RU',
                'password' => '$2y$10$a3id8nthauAEitwVJXPBl.naQtasFPni1ngOnNx7/Lh.DePMrlemu',
                'remember_token' => '6Ts08qDJ6h3NMXO6bBYeqGsOBQrtBJDnfmrmSYvrBGtk1AwJEyM7XkA9ruIz',
                'created_at' => '2017-01-06 15:44:19',
                'updated_at' => '2017-01-06 15:53:55',
            ],
            [
                'login' => 'I.Eremenko',
                'name' => 'Еременко Игорь Николаевич',
                'email' => 'I.Eremenko@BINBank.RU',
                'password' => '$2y$10$Mn0hxNo4k4Dzrmgh9JD6F.wyHdIqcmmF0M/xBtxFLcCdNfSsFGwTu',
                'remember_token' => '',
                'created_at' => '2017-01-10 10:44:12',
                'updated_at' => '2017-01-10 10:44:12',
            ],
            [
                'login' => 'A.Gilinsky',
                'name' => 'Жилинский Александр Болеславович',
                'email' => 'A.Gilinsky@BINBank.RU',
                'password' => '$2y$10$FaB7DUMX73DOEFLM1e0vPOhLbCXCWRyr2QtMPmipcjZ7rIwderbyq',
                'remember_token' => '',
                'created_at' => '2017-01-10 10:44:55',
                'updated_at' => '2017-01-10 10:44:55',
            ],
            [
                'login' => 'V.Ryzhov',
                'name' => 'Рыжов Владимир Витальевич',
                'email' => 'V.Ryzhov@BINBank.RU',
                'password' => '$2y$10$hW1A2vYmO0Ol8alGhk4wqO2p2wnBPI9YEBS6lsE/FnnUwk7MlKxga',
                'remember_token' => '',
                'created_at' => '2017-01-10 10:45:35',
                'updated_at' => '2017-01-10 10:45:35',
            ],
            [
                'login' => 'V.Mihalskiy',
                'name' => 'Михальский Владимир Валерьевич',
                'email' => 'V.Mihalskiy@BINBank.RU',
                'password' => '$2y$10$5Bf5Md62sPTHZBCuTfJFzO23JjXn37jAGYox7Ur2s/4i4SBpb5Uy6',
                'remember_token' => '',
                'created_at' => '2017-01-10 10:46:09',
                'updated_at' => '2017-01-10 10:46:09',
            ],
            [
                'login' => 'p.afinogenov',
                'name' => 'Афиногенов Павел Андреевич',
                'email' => 'p.afinogenov@BINBank.RU',
                'password' => '$2y$10$QYo0O5cCXSF/onq3W9eKEuHMakEw5HD7ejNOIbAXO4RMFNPKA3INS',
                'remember_token' => '',
                'created_at' => '2017-01-10 10:46:53',
                'updated_at' => '2017-01-10 10:46:53',
            ],
            /*[
                'login' => '',
                'name' => '',
                'email' => '',
                'password' => '',
                'remember_token' => '',
                'created_at' => '',
                'updated_at' => '',
            ],*/
        ]);
    }
}
