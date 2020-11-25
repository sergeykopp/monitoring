<?php

namespace Kopp\Console\Commands;

use Illuminate\Console\Command;
use Kopp\Drivers\AdministrationDriver;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restoring Database from XML';

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
        AdministrationDriver::importFromXML();
    }
}
