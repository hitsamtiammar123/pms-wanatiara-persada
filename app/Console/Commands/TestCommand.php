<?php

namespace App\Console\Commands;

use App\Model\Employee;
use App\Model\KPIHeader;
use Illuminate\Console\Command;
use App\Notifications\EndorsementNotification;
use App\Model\User;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hehe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ini command Heheeheeheh';

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
        //
        $e=Employee::find('1915284162');

        print_r($e->getHirarcialEmployee());

    }
}
