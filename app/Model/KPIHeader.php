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
        $curr=Carbon::now();
        $year=$curr->year;
        $month=$curr->month;
        $date=16;
        $curr_date=Carbon::createFromDate($year,$month,$date)->format('Y-m-d');
        return $curr_date;

    }

    public function fetchFrontEndData(){
        $result=[];

        //$header_period_start=self::where('period',$period)->first();

        $period_next_date=Carbon::parse($this->period);
        $period_next_date->addMonth();

        $header_period_end=self::select('id')->where('period',$period_next_date->format('Y-m-d'))->first();

        $kpi_results_header_start=$this->kpiresultsheader;
        $header_end_id=$header_period_end->id;

        foreach($kpi_results_header_start as $kpiresultheader){
            $r=[];
            $kpiresult=KPIResult::find($kpiresultheader->kpi_result_id);
            $kpiresultheaderend=$kpiresultheader->getNext();

            $r['kpi_header_id']=$this->id;
            $r['name']=$kpiresult->name;
            $r['unit']=$kpiresult->unit;

                $r['id']=$kpiresultheader->kpi_result_id;
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

        return $result;

    }

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function kpiendorsements(){
        return $this->hasMany(KPIEndorsement::class,'kpi_header_id','id');
    }

    public function kpiresultsheader(){
        return $this->hasMany(KPIResultHeader::class,'kpi_header_id','id');
    }

    public function kpiprocesses(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocesses_kpiheaders','kpi_header_id','kpi_proccess_id');
    }
}
