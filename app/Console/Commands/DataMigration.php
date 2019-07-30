<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use App\Model\Employee;
use App\Model\Role;
use App\Model\KPIHeader;

class DataMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hitsam:migrate:raw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini dibuat untuk mengkonversikan data dari json mentah ke json yang siap di-seed';

    /**
     * Create a new command instance.
     *
     * @return void
     */

     private $employee_list=[];
     private $jabatan_list=[];
     private $realisasi_list=[];

    public function __construct()
    {
        parent::__construct();
    }

    private function attempt1(){
        $dir='/raw';
        $dir_seed='/seed';
        $disk=Storage::disk('local');
        $raw_users=$disk->get($dir.'/users.json');
        $raw_jabatan=$disk->get($dir.'/jabatan.json');
        $data_users=json_decode($raw_users,true);
        $data_jabatan=json_decode($raw_jabatan,true);

        $result_jabatan=[];
        $result_users=[];

        $result_jabatan=array_map(function($data){
            $data['id']=Role::generateID();
            return $data;
        },$data_jabatan);

        $result_users=array_map(function($data)use($result_jabatan){
            $data['id']=Employee::generateID();
            $jabatan=@$data['jabatan_data'];
            if(isset($jabatan)){
                for($i=0;$i<count($result_jabatan);$i++){
                    $curr=$result_jabatan[$i];
                    if($curr['index']===$jabatan['index']){
                        $data['jabatan_id']=$curr['id'];
                        break;
                    }
                }
            }
            return $data;
        },$data_users);

        $this->employee_list=$result_users;
        $this->jabatan_list=$result_jabatan;

        $disk->put($dir_seed.'/employee.json',json_encode($result_users));
        $disk->put($dir_seed.'/jabatan.json',json_encode($result_jabatan));
    }

    private function attempt2(){
        $disk=Storage::disk('local');
        $users=$disk->get('/raw/users.json');
        $realisasi=$disk->get('/raw/realisasi.json');

        $users_data=json_decode($users,true);
        $realisasi_data=json_decode($realisasi,true);

        $result_realisasi=[];

        $employees=$this->employee_list;
        $realisasi_list=$realisasi_data['list'];

        foreach($employees as $i =>$employee){
            $id=$employee['id'];
            $period_start=date_create('2019-08-16');
            $period_end=date_create('2019-09-16');

            $employee_realisasi=@$realisasi_list[$i];
            $kpi_result=[];
            $result=[];
            $r_total=[];

            for($j=0;isset($employee_realisasi)&&$j<count($employee_realisasi);$j++){
                $curr_r=$employee_realisasi[$j];
                $r=[];
                $r['name']=($curr_r['kpi']!=='')?$curr_r['kpi']:'0';
                $r['unit']=($curr_r['unit']!=='')?$curr_r['unit']:'0';
                $r['pw_1']=($curr_r['pw']['q1']!=='')?$curr_r['pw']['q1']:'0';
                $r['pw_2']=($curr_r['pw']['q2']!=='')?$curr_r['pw']['q2']:'0';
                $r['pt_t1']=($curr_r['pt']['q1']!=='')?$curr_r['pt']['q1']:'0';
                $r['pt_k1']=($curr_r['pt']['q2']!=='')?$curr_r['pt']['q2']:'0';
                $r['pt_t2']=($curr_r['pt']['q3']!=='')?$curr_r['pt']['q3']:'0';
                $r['pt_k2']=($curr_r['pt']['q4']!=='')?$curr_r['pt']['q4']:'0';
                $r['real_t1']=($curr_r['real']['q1']!=='')?$curr_r['real']['q1']:'0';
                $r['real_k1']=($curr_r['real']['q2']!=='')?$curr_r['real']['q2']:'0';
                $r['real_t2']=($curr_r['real']['q3']!=='')?$curr_r['real']['q3']:'0';
                $r['real_k2']=($curr_r['real']['q4']!=='')?$curr_r['real']['q4']:'0';
                $r_total[]=$r;
            }
            $result['employee_id']=$id.'';
            $result['period_start']=$period_start->format('Y-m-d');
            $result['period_end']=$period_end->format('Y-m-d');;
            $result['realisasi']=$r_total;
            $result_realisasi[]=$result;
        }
        $dir_seed='/seed';
        $this->realisasi_list=$result_realisasi;
        $disk->put($dir_seed.'/realisasi.json',json_encode($result_realisasi));
    }

    public function attempt3(){
        $disk=Storage::disk('local');
        $dir='/seed';
        $employees=json_decode($disk->get($dir.'/employee.json'),true);
        $employee_data=[];

        foreach($employees as $employee){
            if(array_key_exists('jabatan_data',$employee)){
                $jabatan=$employee['jabatan_data'];
                $atasan=$jabatan['parent'];
                if($atasan!==-1){
                    $jabatan_atasan_data=$this->jabatan_list[$atasan];
                    if(array_key_exists(0,$jabatan_atasan_data['users'])){
                        $atasan_index=$jabatan_atasan_data['users'][0];
                        $atasan_data=$employees[$atasan_index];
                        $employee['atasan_id']=$atasan_data['id'];
                    }
                }
            }
            $employee_data[]=$employee;
        }
        $disk->put($dir.'/employee.json',json_encode($employee_data));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->attempt1();
        $this->attempt2();
        $this->attempt3();



    }
}
