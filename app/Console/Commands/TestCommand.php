<?php

namespace App\Console\Commands;

use App\Model\KPIResultHeader;
use App\Model\KPITag;
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
        $tag=KPITag::find('1938630853');
        $kpiresultslist=$tag->groupkpiresult;
        $kpiprocesslist=$tag->groupkpiprocess;

        foreach($tag->grouprole as $role){
            foreach($role->employee as $employee){
                $header=$employee->getCurrentHeader();
                $total_result=[];
                $total_process=[];
                foreach($kpiresultslist as $kpiresult){
                    $id=$kpiresult->id;
                    $data=[];
                    switch($id){
                        case '195307034861': 
                            $data=[
                                'kpi_result_id' => $id,
                                'id' => KPIResultHeader::generateID($employee->id,$header->id),
                                'pw' => 15,
                                'pt_t' => 100000,
                                'real_t' =>150000,
                                'pt_k' =>10000,
                                'real_k' =>250000
                            ];
                        break;
                        case '190496534861': 
                            $data=[
                                'kpi_result_id' => $id,
                                'id' => KPIResultHeader::generateID($employee->id,$header->id),
                                'pw' => 15,
                                'pt_t' => 1,
                                'real_t' =>1,
                                'pt_k' =>1,
                                'real_k' =>1
                            ];
                        break;

                    }
                    $total_result[]=$data;
                }
                $header->kpiresultheaders()->createMany($total_result);

                foreach($kpiprocesslist as $kpiprocess){
                    $id=$kpiprocess->id;
                    $total_process[$id]=[
                        'pw' =>20,
                        'pt' => 1,
                        'real' =>1
                    ];
                }
                $header->kpiprocesses()->sync($total_process);
                $this->info("Data untuk \"{$employee->name}\" sudah dibuat ");   
            }
        }


    }
}
