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
        return $this->belongsToMany(KPIResult::class,'kpiresultgroup','tag_id','kpi_result_id')->withTimestamps();
    }

    public function groupkpiprocess(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocessgroup','tag_id','kpi_process_id')->withTimestamps();
    }

    public function grouprole(){
        return $this->belongsToMany(Role::class,'groupingkpi','tag_id','role_id')->withTimestamps();
    }

}
