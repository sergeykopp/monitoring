<?php

namespace Kopp\Console\Commands;

use Illuminate\Console\Command;
use Kopp\Drivers\MailDriver;
use Kopp\Drivers\LogDriver;
use Kopp\Drivers\TroublesDriver;

class SendEmailsForChannel12Hours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:channel12Hours {now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending of emails after 12 hours after registration of problems via communication channels';

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
		$troubles = TroublesDriver::getActualChannels();
		$hours12 = 60 * 60 * 12;
		foreach ($troubles as $trouble) {
			// Поправка при обнулении секунд т.к. на сервере опрос происходит не каждую минуту,
			// а через каждую минуту, поэтому теряется одна минута при переходе на следующий час
			if( ( ('00' == strftime('%S', $now)) and (strftime('%d.%m.%Y %H:%M', $now - $hours12 - 60) == $trouble->started_at) ) or (strftime('%d.%m.%Y %H:%M', $now - $hours12) == $trouble->started_at) ){
				if(!preg_match("/(Инцидент эскалирован на группу Сети)/iu", $trouble->action)) {
					MailDriver::channel12Hours($trouble);
					LogDriver::shedulerTest($startProcess, 'канал связи для эскалации, id=' . $trouble->id);
				}
			}
		}
    }
}
