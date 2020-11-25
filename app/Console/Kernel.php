<?php

namespace Kopp\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'Kopp\Console\Commands\ConsolidatedReport',
        'Kopp\Console\Commands\SendEmailsForChannel12Hours',
        'Kopp\Console\Commands\BackupDatabase',
        'Kopp\Console\Commands\DeleteOldInformation',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		$now = time();
		
		// Ежедневный сводный отчёт
		$schedule->command('emails:consolidatedReport', [$now])
			->everyMinute();
		
		// Отправка писем по истечении 12 часов после регистрации проблем по каналам связи
		// $schedule->command('emails:channel12Hours', [$now])
			// ->everyMinute();
		
		// Резервное копирование базы данных
		$schedule->command('database:backup', [$now])
			->everyMinute();
			
		// Удаление старой информации
		$schedule->command('database:deleteOldInformation')
			->dailyAt(config('settings.deleteOldInformation_time'));
			
		// everyMinute(); Запускать задачу каждую минуту
		// everyFiveMinutes(); Запускать задачу каждые 5 минут
		// everyTenMinutes(); Запускать задачу каждые 10 минут
		// everyThirtyMinutes(); Запускать задачу каждые 30 минут
		// hourly(); Запускать задачу каждый час
		// hourlyAt(17); Запускать задачу каждый час в хх:17 минут
		// daily(); Запускать задачу каждый день в полночь
		// dailyAt('13:00'); Запускать задачу каждый день в 13:00
		// twiceDaily(1, 13); Запускать задачу каждый день в 1:00 и 13:00
		// weekly(); Запускать задачу каждую неделю
		// monthly(); Запускать задачу каждый месяц
		// monthlyOn(4, '15:00'); Запускать задачу 4 числа каждого месяца в 15:00
		// quarterly(); Запускать задачу каждые 3 месяца
		// yearly(); Запускать задачу каждый год
		// timezone('America/New_York'); Задать часовой пояс
		
		// ->weekdays(); Ограничить задачу рабочими днями
		// ->sundays(); Ограничить задачу воскресеньем
		// ->mondays(); Ограничить задачу понедельником
		// ->tuesdays(); Ограничить задачу вторником
		// ->wednesdays(); Ограничить задачу средой
		// ->thursdays(); Ограничить задачу четвергом
		// ->fridays(); Ограничить задачу пятницей
		// ->saturdays(); Ограничить задачу субботой
		// ->between($start, $end); Ограничить запуск задачи между временем начала и конца промежутка
		// ->when(Closure); Ограничить задачу на основе успешного теста
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
