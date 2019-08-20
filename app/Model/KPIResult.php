<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;


class KPIResult extends Model
{
    //
    use DynamicID;

    protected $table='kpiresults';
    protected $fillable=[
        'name','kpi_header_id','unit'
    ];
    protected $casts=['id'=>'string'];
    protected $hidden=['created_at','updated_at'];
    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at'];

    public static function generateID($employeeID){
        $employee=Employee::find($employeeID);

        if(!$employee){
            return null;
        }

        $employee_index=$employee->getIndex();
        $rand_num=rand(10,99);

        $a=3;
        $code=add_zero($employee_index,1).$rand_num;

        return self::_generateID($a,$code);
    }

    public function kpiresultheaders(){
        return $this->hasMany(KPIResultHeader::class,'kpi_result_id','id');
    }

}
