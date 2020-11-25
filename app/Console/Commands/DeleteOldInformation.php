<?php

namespace Kopp\Console\Commands;

use Illuminate\Console\Command;
use Kopp\Drivers\CityDriver;
use Kopp\Drivers\DirectorateDriver;
use Kopp\Drivers\FilialDriver;
use Kopp\Drivers\OfficeDriver;
use Kopp\Drivers\TroublesDriver;
use Kopp\Drivers\LogDriver;

class DeleteOldInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:deleteOldInformation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Old Information';

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
		$startProcess = time();
		TroublesDriver::deleteOldTroubles();
		OfficeDriver::deleteNotActualOffices();
		CityDriver::deleteNotActualCities();
		FilialDriver::deleteNotActualFilials();
		DirectorateDriver::deleteNotActualDirectorates();
		LogDriver::deleteOldTroubles();
		LogDriver::shedulerTest($startProcess, 'удаление старой информации');
    }
}
