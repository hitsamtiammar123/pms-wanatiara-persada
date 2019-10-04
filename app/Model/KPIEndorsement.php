<?php

namespace App\Model;

use App\Model\Interfaces\Endorseable;
use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Database\Eloquent\SoftDeletes;

class KPIEndorsement extends Model
{
    //

    use DynamicID,SoftDeletes;
    protected $table='kpiendorsements';
    protected $fillable=[
        'id','kpi_header_id','employee_id','level'
    ];
    protected $casts=['id'=>'string'];
    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at'];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function kpiheader(){
        return $this->belongsTo(KPIHeader::class,'kpi_header_id','id');
    }

    public static function generateID($employeeID){
        $employee=Employee::find($employeeID);

        if(!$employee){
            return null;
        }

        $employee_index=$employee->getIndex();
        $rand_num=rand(20,99);

        $a=5;
        $code=add_zero($employee_index,1).$rand_num;

        return self::_generateID($a,$code);
    }

    public static function fetchFromHirarcialArr(array $hirarcial_employees,Endorseable $endorseable){
        $r=[];

        foreach($hirarcial_employees as $index => $employee){
            $employee->level=$index+1;
            $employee->role;
            $employee->verified=$endorseable->hasEndorse($employee);
            unset($employee->atasan);
            unset($employee->user);
            $r[$index+1]=$employee;
        }
        return $r;
    }
}
