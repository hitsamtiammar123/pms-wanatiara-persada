<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Support\Carbon;
use App\Model\Traits\Indexable;

class KPIHeader extends Model
{
    //
    use DynamicID,Indexable;

    protected static $listID=[];
    protected $table='kpiheaders';
    protected $fillable=[
        'period'
    ];

    protected $casts=['id'=>'string'];

    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at'];

    protected function fetchKPIResult(){

        $result=[];
        $kpi_results_header_start=$this->kpiresultheaders->sortBy('created_at');

        foreach($kpi_results_header_start as $kpiresultheader){
            $r=[];
            $kpiresult=KPIResult::find($kpiresultheader->kpi_result_id);
            $kpiresultheaderend=$kpiresultheader->getNext();

            if($kpiresultheaderend){
                $r['kpi_header_id']=$this->id;
                $r['name']=$kpiresult->name;
                $r['unit']=$kpiresult->unit;

                $r['id']=$kpiresultheader->id;
                $r['pw_1']=$kpiresultheader->pw;
                $r['pw_2']=$kpiresultheaderend->pw;
                $r['pt_t1']=$kpiresultheader->pt_t;
                $r['pt_k1']=$kpiresultheader->pt_k;
                $r['pt_t2']=$kpiresultheaderend->pt_t;
                $r['pt_k2']=$kpiresultheaderend->pt_k;
                $r['real_t1']=$kpiresultheader->real_t;
                $r['real_k1']=$kpiresultheader->real_k;
                $r['real_t2']=$kpiresultheaderend->real_t;
                $r['real_k2']=$kpiresultheaderend->real_k;

                $result[]=$r;
            }

        }

        return $result;
    }

    protected function fetchKPIProcess(){
        $result=[];
        $kpi_proccess_start=$this->kpiprocesses;

        foreach($kpi_proccess_start as $curr_s){
            $r=[];
            $curr_e=$curr_s->getNext();

            if($curr_e){
                $r['id']=$curr_s->id;
                $r['name']=$curr_s->name;
                $r['unit']=$curr_s->unit;
                $r['pw_1']=$curr_s->pivot->pw;
                $r['pw_2']=$curr_e->pivot->pw;
                $r['pt_1']=$curr_s->pivot->pt;
                $r['pt_2']=$curr_e->pivot->pt;
                $r['real_1']=$curr_s->pivot->real;
                $r['real_2']=$curr_e->pivot->real;
                $result[]=$r;
            }

        }
        return $result;
    }

    public static function generateID($employeeID){
        $employee=Employee::find($employeeID);

        if(!$employee){
            return null;
        }

        $employee_index=$employee->getIndex();
        $header_count=$employee->kpiheaders()->count();

        $a=4;
        $code=add_zero($employee_index,1).add_zero($header_count,1);

        return self::_generateID($a,$code);

    }

    public static function getDate($month){
        $curr=Carbon::now();
        $year=$curr->year;
        $month=$month;
        $date=16;
        $curr_date=Carbon::createFromDate($year,$month,$date)->format('Y-m-d');
        return $curr_date;
    }

    public static function getCurrentDate(){
        $curr_date=self::getDate(Carbon::now()->month);
        return $curr_date;

    }

    public function fetchFrontEndData($type){

        if($type==='kpiresult'){
            return $this->fetchKPIResult();
        }
        else if($type==='kpiprocess'){
            return $this->fetchKPIProcess();
        }
        return null;


    }

    public function pushNewKPIResult($kpiresult_id){
        $next_header=$this->getNext();

        $kpi_result_header=new KPIResultHeader();
        $kpi_result_header_2=new KPIResultHeader();

        $kpi_result_header->id=KPIResultHeader::generateID($this->employee->id,$this->id);
        $kpi_result_header->kpi_result_id=$kpiresult_id;

        $kpi_result_header_2->id=KPIResultHeader::generateID($this->employee->id,$this->id);
        $kpi_result_header_2->kpi_result_id=$kpiresult_id;

        $this->kpiresultheaders()->save($kpi_result_header);
        $next_header?$next_header->kpiresultheaders()->save($kpi_result_header_2):'';
    }

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function kpiendorsements(){
        return $this->hasMany(KPIEndorsement::class,'kpi_header_id','id');
    }

    public function kpiresultheaders(){
        return $this->hasMany(KPIResultHeader::class,'kpi_header_id','id');
    }

    public function kpiprocesses(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocesses_kpiheaders','kpi_header_id','kpi_proccess_id')
        ->withTimestamps()->withPivot(['pw','pt','real']);
    }

    public function getNext(){
        $date=Carbon::parse($this->period);
        $next_date=$date->addMonth();

        $r=self::where('period',$next_date)->where('employee_id',$this->employee_id)->first();

        return $r;
    }
}
