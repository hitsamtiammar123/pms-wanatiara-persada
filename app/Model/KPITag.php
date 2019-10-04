<?php

namespace App\Model;

use App\Model\Traits\DynamicID;
use App\Model\Traits\Indexable;
use Illuminate\Database\Eloquent\Model;

class KPITag extends Model
{

    use DynamicID,Indexable;

    protected $table='kpitags';
    protected $fillable=[
        'id','name','representative_id'
    ];
    protected $casts=[
        'id' => 'string'
    ];
    protected $hidden=['created_at','updated_at','deleted_at'];

    public static function generateID(){
        $a=8;

        return self::_generateID($a);
    }

    public function representative(){
        return $this->belongsTo(Employee::class,'representative_id','id');
    }

    public function groupkpiresult(){
        return $this->belongsToMany(KPIResult::class,'kpiresultgroup','tag_id','kpi_result_id');
    }

    public function groupkpiprocess(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocessgroup','tag_id','kpi_process_id');
    }

    public function groupemployee(){
        return $this->belongsToMany(Employee::class,'groupingkpi','tag_id','employee_id')->withTimestamps();
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

}
