<?php

namespace App\Model;

use App\Model\Interfaces\Endorseable;
use App\Model\Traits\DynamicID;
use App\Model\Traits\Indexable;
use Illuminate\Database\Eloquent\Model;
use App\Model\Employee;
use Illuminate\Support\Carbon;

class KPITag extends Model implements Endorseable
{

    use DynamicID,Indexable;

    protected $table='kpitags';
    protected $fillable=[
        'id','name','representative_id','period'
    ];
    protected $casts=[
        'id' => 'string'
    ];
    protected $hidden=['created_at','updated_at','deleted_at'];
    protected $date;

    public static function generateID(){
        $a=8;

        return self::_generateID($a);
    }

    public function representative(){
        return $this->belongsTo(Employee::class,'representative_id','id');
    }

    public function groupkpiresult(){
        return $this->belongsToMany(KPIResult::class,'kpiresultgroup','tag_id','kpi_result_id')->withTimestamps();;
    }

    public function groupkpiprocess(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocessgroup','tag_id','kpi_process_id')->withTimestamps();
    }

    public function groupemployee(){
        return $this->belongsToMany(Employee::class,'groupingkpi','tag_id','employee_id')->withTimestamps();
    }

    public function getZeroIndexEmployee(){
        return $this->groupemployee[0];
    }

    public function createHeaderIfNotExists($period=null){
        $period=is_null($period)?KPIHeader::getCurrentDate():$period;
        $cPeriod=Carbon::parse($period);

        $m=$cPeriod->month;$y=$cPeriod->year;
        $num=0;
        foreach($this->groupemployee as $employee){
            $header=$employee->getHeader($m,$y);
            $pw_total_result=round(100/$this->groupkpiresult->count(),2);
            $pw_total_process=round(100/$this->groupkpiprocess->count(),2);
            foreach($this->groupkpiresult as $kpiresult){
                $kpiresult_id=$kpiresult->id;
                $check=$header->kpiresultheaders()->where('kpi_result_id',$kpiresult)->first();
                if(is_null($check)){
                    $header->kpiresultheaders()->create([
                        'id'=>KPIResultHeader::generateID($employee->id,$header->id),
                        'kpi_result_id'=>$kpiresult_id,
                        'pw'=>$pw_total_result
                    ]);
                    $num++;
                }
            }

            foreach($this->groupkpiprocess as $kpiprocess){
                $kpiprocess_id=$kpiprocess->id;
                $check=$header->kpiprocesses()->find($kpiprocess_id);
                if(is_null($check)){
                    $header->kpiprocesses()->attach([
                        $kpiprocess_id => [
                            'pw'=>$pw_total_process
                        ]
                    ]);
                    $num++;
                }
            }
        }

        return $num;

    }

    public function updateWeightingFromArr(array $weighting){
        $weight_result=array_key_exists('weight_result',$weighting)?
        $weighting['weight_result']:null;
        $weight_process=array_key_exists('weight_process',$weighting)?
        $weighting['weight_process']:null;

        foreach($this->groupemployee as $employee){
            $header=$employee->getCurrentHeader();
            if($header){
                $weight_result?$header->weight_result=$weight_result/100:null;
                $weight_process?$header->weight_process=$weight_process/100:null;
                $header->save();
            }
        }
    }

    public function hasEndorse(Employee $employee){
        $d=$this->date?$this->date:KPIHeader::getCurrentDate();
        foreach($this->groupemployee as $curr){
            $header=$curr->kpiheaders()->where('period',$d)->first();
            if($this->representative->getEndorsementLevel($employee)==1)
                $curr_e=$curr->isUser()?$curr:$employee;
            else
                $curr_e=$employee;
            $r=$header->kpiendorsements()->where('employee_id',$curr_e->id)->first();
            if(is_null($r))
                return false;
        }
        return true;
    }

    public function fetchKPIEndorsement($date=null){
        $this->date=$date;
        return KPIEndorsement::fetchFromHirarcialArr(
            $this->representative->getHirarcialEmployee(),$this
        );
    }

}
