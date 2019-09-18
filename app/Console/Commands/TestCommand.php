<?php

namespace App\Console\Commands;

use App\Model\Employee;
use App\Model\KPIHeader;
use App\Model\KPIResult;
use App\Model\KPIResultHeader;
use Illuminate\Console\Command;

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
        foreach(KPIResultHeader::all() as $r){
            $r->mapPriviledge(100);
        }
    //    $r1=KPIResultHeader::find('19365656155758');
    //    printf($r1->isPriviledge());
    }
}
