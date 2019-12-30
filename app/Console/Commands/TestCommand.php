<?php

namespace App\Console\Commands;

use App\Model\KPIResultHeader;
use App\Model\KPITag;
use App\Model\KPIHeader;
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

    public function syncKPIHeader($period){
        $headers=KPIHeader::where('period','2019-10-16')->get();
        foreach($headers as $header){
            $curr_header=$header->getPrev();
            $header->kpiresultheaders->count()==!0?:$header->makeKPIResult($curr_header);
            $header->kpiprocesses->count()==!0?:$header->makeKPIProcess($curr_header);
            printf("Header dari %s untuk period %s sudah dibuat KPInya\n",$header->employee->name,$period);
            sleep(1);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$this->syncKPIHeader('2019-10-16');
        echo 'test';
    }
}
