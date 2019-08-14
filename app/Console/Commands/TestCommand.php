<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Excel;
use App\Imports\ArrayImport;

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
        $path='C:\\xampp\\htdocs\\pms-wanatiara-persada-v1-laravel\\storage\\requirement\\Target Managemen 2019.xlsx';


    }
}
