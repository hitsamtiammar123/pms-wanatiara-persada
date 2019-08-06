<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Support\Carbon;

class KPIResultHeader extends Model
{
    //

    use DynamicID;

    protected $table='kpiresultsheader';
    protected $casts=['id'=>'string'];
    protected $hidden=['created_at','updated_at'];
    protected $fillable=['pw','pt_t','pt_k','real_t','real_k'];

    public static function generateID($employeeID,$headerID){
        $employee=Employee::find($employeeID);
        $header=KPIHeader::find($headerID);

        if(!$employee ||!$header){
            return null;
        }

        $employee_index=$employee->getIndex().$header->getIndex();
        $rand_num=rand(10,99);

        $a=6;
        $code=add_zero($employee_index,1).$rand_num;

        return self::_generateID($a,$code);
    }

    public function kpiresult(){
        return $this->belongsTo(KPIResult::class,'kpi_result_id','id');
    }

    public function kpiheader(){
        return $this->belongsTo(KPIHeader::class,'kpi_header_id','id');
    }

    public function getNext(){
        $kpiheader=$this->kpiheader;
        $period=$kpiheader->period;

        $carbon_p=Carbon::parse($period);
        $next_p=$carbon_p->addMonth();

        $next_kpiheader=KPIHeader::select('id')->where('employee_id',$kpiheader->employee_id)
        ->where('period',$next_p)->first();

        $next_kpiresultheader=self::where('kpi_header_id',$next_kpiheader->id)->where('kpi_result_id',$this->kpi_result_id)->first();

        return $next_kpiresultheader;
    }
}
