<?php

namespace App\Console\Commands;

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

    public function calculateCum(){
        $kpiresultheaders=KPIResultHeader::get();
        $kpiresultheaders=$kpiresultheaders->filter(function($d){
            return $d->kpiresult->unit==='$';
        });

        foreach($kpiresultheaders as $resultheader){
            $prev=$resultheader->getPrev();
            if($prev){
                $pt_k1=$prev->pt_k;
                $pt_t2=$resultheader->pt_t;
                $real_k1=$prev->real_k;
                $real_t2=$resultheader->real_k;

                $resultheader->pt_k=intval($pt_k1+$pt_t2).'';
                $resultheader->real_k=intval($real_k1+$real_t2).'';

                $header=$resultheader->kpiheader;
                $this->info("Untuk Sasaran Proses dengan nama \"{$resultheader->kpiresult->name}\" milik {$header->employee->name} untuk periode {$header->period}");
                $resultheader->save();
                sleep(1);

            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //


    }
}
