<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;

class KPIEndorsement extends Model
{
    //

    use DynamicID;
    protected $table='kpiendorsements';
    protected $fillable=[
        'kpi_header_id','employee_id','verified'
    ];  

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
        $code=add_zero($employee_index,1);

        return self::_generateID($a,$code);
    }
}
