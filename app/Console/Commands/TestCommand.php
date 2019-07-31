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

    private function test1(){
        $p=add_zero(1);
        $arr_result=[]; 
        for($i=1;$i<=100;$i++){
            $a=1;
            $r=generate_id($a,23);
            while(in_array($r,$arr_result)){
                $r=generate_id(++$a,23);
            }
               
            $arr_result[]=$r;
            echo "Percobaan {$i}: {$r} -> {$a}\n";
        }
    }

    private function test2(){
        $p=add_zero(1);
        $arr_result=[]; 
        for($i=1;$i<=100;$i++){
            $id=User::generateID();
            $arr_result[]=$id;
            echo "{$i}=>{$id}\n";
        }

        $c1=array_unique($arr_result);
        if(count($arr_result)===count($c1)){
            echo "Semua Element Dalam Array Tidak ada yang sama";
        }
        else
            echo 'Ada Elemen yang nilainya sama';
    }

    private function test3(){
        $c=Employee::all();

        foreach($c as $i =>$data){
            $id=$data->id;
            $header_id=KPIResult::generateID($id);
            printf("%d KPI Result dari %s adalah %s \n",$i,$id,$header_id);
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
        $data=Employee::where('name','Feng Weibin')->first();
        $kpiheader=$data->kpiheaders[0];
        // for($i=0;$i<100;$i++){
        //     $id=KPIResult::generateID('1951325166');
        //     $this->info("{$i} id= {$id}");
        // }
        $result=$kpiheader->kpiresults->toArray();
        echo "TEST 123 \n";
        sleep(2);
        dd($result);

    }
}
