<?php

use Illuminate\Database\Seeder;
use Kopp\Models\Source;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Source::insert([
            ['name' => '... другой источник'],
            ['name' => 'Power Wizard - Мониторинг ДГУ на Добролюбова 16'],
            ['name' => 'Power Wizard - Мониторинг ДГУ на Инской 54'],
            ['name' => 'SCOM - Мониторинг температуры'],
            ['name' => 'STULZ - Пульт управления кондиционерами'],
            ['name' => 'TAC Vista Workstation - Мониторинг ЦОД на Окской'],
            ['name' => 'WEB - NeWave мониторинг UPS на Добролюбова 16'],
            ['name' => 'WEB - NeWave мониторинг UPS на Инской 54'],
            ['name' => 'WEB - Мониторинг СПЭД'],
            ['name' => 'WEB - Мониторинг баз данных'],
            ['name' => 'WEB - Мониторинг процессинга'],
            ['name' => 'WEB - Мониторинг температуры в серверных помещениях'],
            ['name' => 'Звонки пользователей'],
            ['name' => 'Звонок Администратора ОФБДиБ'],
            ['name' => 'Звонок дилера'],
            ['name' => 'Звонок из Контакт-Центра'],
            //['name' => 'Письмо от ExchangeSCOM@mdmbank.com'],
            //['name' => 'Письмо от HostTracker Notifier'],
            //['name' => 'Письмо от JOB_MONITOR RBOBASE'],
            //['name' => 'Письмо от MessageBroker@mdmbank.com'],
            ['name' => 'Письмо от Oracle_gridt@mdmbank.com'],
            ['name' => 'Письмо от SCOM@mdmbank.com'],
            //['name' => 'Письмо от Space monitor...'],
            //['name' => 'Письмо от cardmail@mdmbank.com'],
            ['name' => 'Письмо от dbservice@mdmbank.com'],
            //['name' => 'Письмо от hibara@plcard.mdmbank.ru'],
            ['name' => 'Письмо от hostmonitor@mdmbank.com'],
            //['name' => 'Письмо от mailathena@mdmbank.com'],
            //['name' => 'Письмо от nobody@mdmbank.com'],
            //['name' => 'Письмо от node@nsk-sabcard...'],
            //['name' => 'Письмо от oracleemagent@mdmbank.com'],
            //['name' => 'Письмо от root@meeb.corp.icba.biz'],
            //['name' => 'Письмо от sched@uler.com'],
            //['name' => 'Письмо от sysHedShdl@mdmbank.com'],
            //['name' => 'Письмо от sysOWSAlerter@mdmbank.com'],
            ['name' => 'Письмо от telecom@mdmbank.com'],
            ['name' => 'Письмо от zabbix@mdmbank.com'],
            ['name' => 'Регламентные процедуры'],
        ]);
    }
}
