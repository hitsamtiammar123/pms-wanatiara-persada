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
        'name','kpi_header_id','unit','pw_1','pw_2','pt_t1','pt_k1','pt_t2','pt_k2',
        'real_t1','real_k1','real_t2','real_k2'
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

    public function kpiheader(){
        return $this->belongsTo(KPIHeader::class,'kpi_header_id','id');
    }

}
