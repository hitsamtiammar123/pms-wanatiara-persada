<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;


class KPIProcess extends Model
{
    //
    use DynamicID;
    protected $table='kpiprocesses';
    protected $casts=['id'=>'string'];
    protected $hidden=['created_at','updated_at'];

    const HIDDEN_PIVOT_PROPERTY=['created_at','updated_at','kpi_header_id','kpi_proccess_id'];
    const FRONT_END_PROPERTY=['pw_1','pw_2','pt_1','pt_2','real_1','real_2'];

    public static function generateID(){
        $a=7;

        return self::_generateID($a);
    }


    public function kpiheaders(){
        return $this->belongsToMany(KPIHeader::class,'kpiprocesses_kpiheaders','kpi_proccess_id','kpi_header_id');
    }


    public function getNext(){
        $kpiheader=KPIHeader::find($this->pivot->kpi_header_id);
        if($kpiheader){
            $kpiheadernext=$kpiheader->getNext();
            if($kpiheadernext){
                $kpiprocessNext=$kpiheadernext->kpiprocesses->where('id',$this->id)->first();
                return $kpiprocessNext;
            }
            else {
                return null;
            }
        }
        return null;
    }
}
