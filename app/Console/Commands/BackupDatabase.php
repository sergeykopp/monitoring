<?php

namespace Kopp\Console\Commands;

use Illuminate\Console\Command;
use Kopp\Drivers\AdministrationDriver;
use Kopp\Drivers\LogDriver;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup {now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backing Up the Database';

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
        // Поправка при обнулении секунд т.к. на сервере опрос происходит не каждую минуту,
		// а через каждую минуту, поэтому теряется одна минута при переходе на следующий час
		if( ( ('00' == strftime('%S', $now)) and (strftime('%H:%M', $now - 60) == config('settings.auto_backup_time')) ) or (strftime('%H:%M', $now) == config('settings.auto_backup_time')) ){
			AdministrationDriver::exportToXML();
			LogDriver::shedulerTest($startProcess, 'резервное копирование базы данных');
		}
    }
}
