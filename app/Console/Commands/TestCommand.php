<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

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
        //$this->syncKPIHeader('2019-10-16');

        $path=resource_path('kpicompany/test.json');
        $f=fopen($path,'w+');

        if($f){
            \fputs($f,'{"test":"test"}');
            echo 'berhasil';
        }
        else
            echo 'gagal';
        fclose($f);


    }
}
