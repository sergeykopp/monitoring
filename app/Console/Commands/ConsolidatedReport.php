<?php

namespace Kopp\Console\Commands;

use Illuminate\Console\Command;
use Kopp\Drivers\MailDriver;
use Kopp\Drivers\LogDriver;
use Kopp\Drivers\ReportsDriver;

class ConsolidatedReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:consolidatedReport {now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily consolidated report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$now = $this->argument('now'); // Получение параметра $now из основного шедулера Kernel
		$startProcess = time();
        $fileName = preg_replace("/\./u", ' ' . strftime('%d %m', $now) . '.', config('settings.triggers_reports'));
		// Поправка при обнулении секунд т.к. на сервере опрос происходит не каждую минуту,
		// а через каждую минуту, поэтому теряется одна минута при переходе на следующий час
		if( ( ('00' == strftime('%S', $now)) and (strftime('%H:%M', $now - 60) == config('settings.consolidatedReport_time')) ) or (strftime('%H:%M', $now) == config('settings.consolidatedReport_time')) ) {
			$troubles = ReportsDriver::getTroublesForConsolidatedReport();
			// Файл отчёта из Zabbix
			$reportFileFlag = false;
			// $reportFileFlag = ReportsDriver::consolidatedReport($fileName);
			if(false == $reportFileFlag) {
				MailDriver::consolidatedReport($troubles, null);
			} else {
				MailDriver::consolidatedReport($troubles, $fileName);
			}
			LogDriver::shedulerTest($startProcess, 'ежедневный сводный отчёт');
		}
    }
}
