<?php

namespace App\Console\Commands;

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
        $s='test';
        $t='234';
        $str="ini hasil dari test %s, ini hasil dari t %s";

        $result=sprintf($str,$s,$t);

        echo $result;

    }
}
