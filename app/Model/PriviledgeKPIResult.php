<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PriviledgeKPIResult extends Model
{
    protected $table='priviledgekpiresults';
    protected $hidden=['created_at','id'];

    protected $fillable=[
        'value','by','priviledge'
    ];

    public function kpiresultheaders(){
        return $this->belongsToMany(KPIResultHeader::class,'priviledgedetail','p_id','h_id');
    }
}
