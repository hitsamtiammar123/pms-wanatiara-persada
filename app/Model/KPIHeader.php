<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
class KPIHeader extends Model
{
    //
    use DynamicID;

    protected static $listID=[];
    protected $table='kpiheaders';
    protected $fillable=[
        'period_start','period_end'
    ];

    public static function generateID($employeeID){
        $employee=Employee::find($employeeID);
        
        if(!$employee){
            return null;
        }

        $employee_index=$employee->getIndex();
        $header_count=$employee->kpiheaders()->count();

        $a=4;
        $code=add_zero($employee_index,1);

        return self::_generateID($a,$code);

    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function kpiendorsements(){
        return $this->hasMany(KPIEndorsement::class,'kpi_header_id','id');
    }

    public function kpiresults(){
        return $this->hasMany(KPIResult::class,'kpi_header_id','id');
    }

    public function kpiprocesses(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocesses_kpiheaders','kpi_header_id','kpi_proccess_id');
    }
}
