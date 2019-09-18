<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PriviledgeKPIResult extends Model
{
    protected $table='priviledgeresultskpia';
    protected $hidden=['created_at','id'];

    protected $fillable=[
        'value'
    ];

    public function kpiresultheaders(){
        return $this->belongsTo(KPIResultHeader::class,'kpi_header_result_id','id');
    }

}
