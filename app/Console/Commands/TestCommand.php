<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Library\TestLib;
use Storage;
use App\Model\Role;
use App\Model\User;
use App\Model\Employee;
use App\Model\KPIHeader;
use App\Model\KPIResult;
use App\Model\KPIEndorsement;
use App\Model\KPIResultHeader;
use DB;

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
        //$this->info(KPIResultDetail::generateID('2019-08-16'));
        $disk=Storage::disk('web');
        $files=$disk->allfiles('controller');
        dd($files);

    }
}
