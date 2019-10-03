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

    const KPIPROCESSCURRKEY=[
        'pw'=>'pw_2',
        'pt'=>'pt_2',
        'real'=>'real_2'
    ];

    const KPIPROCESSPREVKEY=[
        'pw'=>'pw_1',
        'pt'=>'pt_1',
        'real'=>'real_1'
    ];

    const KPIPROCESSORIGINALKEY=[
        'pw' => 'pivot.pw',
        'pt' => 'pivot.pt',
        'real'=>'pivot.real'
    ];

    protected function getFromHeader($header){
        if($header){
            $kpiprocess=$header->kpiprocesses->where('id',$this->id)->first();
            return $kpiprocess;
        }
        else {
            return null;
        }
    }

    public static function getArrayMap(array $mapping,$kpiprocess){
        $r=[];
        foreach($mapping as $key=>$map){
            if(array_key_exists($map,$kpiprocess))
               $r[$key]=$kpiprocess[$map];
        }
        return $r;
    }

    public static function generateID(){
        $a=7;

        return self::_generateID($a);
    }


    public function kpiheaders(){
        return $this->belongsToMany(KPIHeader::class,'kpiprocesses_kpiheaders','kpi_proccess_id','kpi_header_id')->withPivot(['pw','pt','real','kpi_header_id']);
    }

    public function getPrev(){
        if($this->pivot){
            $kpiheader=KPIHeader::find($this->pivot->kpi_header_id);
            if($kpiheader){
                $kpiheaderprev=$kpiheader->getPrev();
                return $this->getFromHeader($kpiheaderprev);
            }
        }
        return null;
    }

    public function getNext(){
        if($this->pivot){
            $kpiheader=KPIHeader::find($this->pivot->kpi_header_id);
            if($kpiheader){
                $kpiheadernext=$kpiheader->getNext();
                return $this->getFromHeader($kpiheadernext);
            }
        }
        return null;
    }

    /**
     * Mapping data KPIProcess dari array
     *
     * @param array $mapping
     * @param array|Illuminate\Support\Collection $kpiresult
     * Data KPIProcess yang mau dimasukan
     * @return void
     */
    public function mapFromArr(array $mapping,$kpiprocess){

        foreach($mapping as $key=>$map){
            if(array_key_exists($map,$kpiprocess))
               $this->pivot->{$key}=$kpiprocess[$map];
        }
        $this->pivot->save();
    }
}
