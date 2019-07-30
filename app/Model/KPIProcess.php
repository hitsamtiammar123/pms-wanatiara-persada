<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
class KPIProcess extends Model
{
    //
    use MyTimeZone,DynamicID;
    protected $table='kpiprocesses';

    public function kpiheaders(){
        return $this->belongsToMany(KPIHeader::class,'kpiprocesses_kpiheaders','kpi_proccess_id','kpi_header_id');
    }
}
