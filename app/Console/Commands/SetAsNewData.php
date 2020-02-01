<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\KPIHeader;
use App\Model\Employee;

class SetAsNewData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:set-as-new-data {employee_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command ini berfungsi untuk men-set data KPIResultHeader menjadi data baru';


    protected function setAsNewData($employee){
        $count_kpiresult_header=0;
        $headers=$employee->kpiheaders->sortBy('period');
        foreach($headers as $header){
            foreach($header->kpiresultheaders as $resultheader){
                try{
                    $resultheader->setAsNewData(true);
                    $resultheader->save();
                    $count_kpiresult_header++;
                }catch(Exception $err){
                    echo 'Terdapat error pada saat mengubah data untuk '.$employee->name.' di periode ke-'.$header->period."\n";
                }
            }
        }
        echo 'Header untuk '.$employee->name.' sudah di set yang terbaru. Jumlah data yang di-set='.$count_kpiresult_header."\n";
    }

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

        $employee_id=$this->argument('employee_id');

        if($employee_id){
            $employee=Employee::find($employee_id);
            $this->setAsNewData($employee);
        }
        else{
            $employees=Employee::all();

            foreach($employees as $employee){
                $this->setAsNewData($employee);
            }
        }

    }
}
