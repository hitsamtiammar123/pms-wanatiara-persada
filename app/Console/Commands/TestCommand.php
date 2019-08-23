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
        $user=User::find('1950300028');

        $notification=$user->unreadNotifications->where('id','6dfac035-99c9-439e-b047-3fc9345cba9d')->first();

        dd($notification->toArray());

    }
}
