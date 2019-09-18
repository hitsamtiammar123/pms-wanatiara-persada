<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Support\Carbon;

class KPIResultHeader extends Model
{
    //

    use DynamicID;

    protected $table='kpiresultsheader';
    protected $casts=[
        'id'=>'string',
        'kpi_result_id'=>'string',
        'kpi_header_id'=>'string'
    ];
    protected $hidden=['created_at','updated_at'];
    protected $fillable=['id','pw','pt_t','pt_k','real_t','real_k','kpi_header_id','kpi_result_header','id','kpi_result_id'];

    const FRONT_END_PROPERTY=['pw_1','pw_2','pt_t1','pt_k1','pt_t2','pt_k2','real_t1','real_k1','real_t2','real_k2'];


    public static function generateID($employeeID,$headerID){
        $employee=Employee::find($employeeID);
        $header=KPIHeader::find($headerID);

        if(!$employee ||!$header){
            return null;
        }

        $employee_index=$employee->getIndex().$header->getIndex();
        $rand_num=rand(10,99);

        $a=6;
        $code=add_zero($employee_index,1).$rand_num;

        return self::_generateID($a,$code);
    }

    public static function deleteFromArray($kpiresultdeletelist){
        foreach($kpiresultdeletelist as $todelete){
            $curr_delete=self::find($todelete);
            if($curr_delete){
                $curr_delete->delete();
            }
        }
    }



    /**
     *
     * Method ini berfungsi untuk melakukan kalkulasi Data Kumulatif pada tanggal yang bersangkutan
     * @return void
     */
    public static function calculateCum(){
        $headers=KPIHeader::orderBy('period')->get();

        foreach($headers as $header){
            $resultheaders=$header->kpiresultheaders;
            $resultheaders=$resultheaders->filter(function($d){
                return $d->kpiresult->unit==='$';
            });
            foreach($resultheaders as $resultheader){
                $prev=$resultheader->getPrev();
                if($prev){
                    $pt_k1=$prev->pt_k;
                    $pt_t2=$resultheader->pt_t;
                    $real_k1=$prev->real_k;
                    $real_t2=$resultheader->real_t;

                    $resultheader->pt_k=intval($pt_k1+$pt_t2).'';
                    $resultheader->real_k=intval($real_k1+$real_t2).'';
                    $resultheader->save();
                }

            }

        }
    }


    public function kpiresult(){
        return $this->belongsTo(KPIResult::class,'kpi_result_id','id');
    }

    public function kpiheader(){
        return $this->belongsTo(KPIHeader::class,'kpi_header_id','id');
    }

    public function priviledge(){
        return $this->hasOne(PriviledgeKPIResult::class,'kpi_header_result_id','id');
    }

    /**
     * Menentukan apakah kpiresult ini bersifat priviledge
     *
     * @return bool
     */
    public function isPriviledge(){
        $unit=$this->kpiresult->unit;
        $pt_t=$this->pt_t;
        if($unit==='#' && $pt_t==0)
            return true;
        else
            return false;

    }

    /**
     * Melakukan mapping jika data ini memiliki priviledge
     *
     * @param string|null $value
     * @return void
     */
    public function mapPriviledge($value=null){
        if($this->isPriviledge()){
            if(is_null($this->priviledge)){
                $this->priviledge()->create([
                    'value'=>$value
                ]);
            }
            else{
                $p=$this->priviledge;
                $p->value=$value;
                $p->save();
            }
        }
        else{
            if(!is_null($this->priviledge))
                $this->priviledge->delete();
        }
    }

    /**
     *
     * @param array $kpiresult data dari kpiresult yang bersangkutan
     * @param App\Model\KPIResultHeader $prev data dari kpiresult yang sebelumnya
     * @return array
     */
    public function fetchFrontEndPriviledge(array $kpiresult,KPIResultHeader $prev){
        if($this->isPriviledge()){
            $priviledge=$this->priviledgekpiresults[0];
            $key_1=$priviledge->pivot->key.'2';
            $kpiresult[$key_1]=$priviledge->pivot->value;
            if($prev->isPriviledge()){
                $priviledge=$prev->priviledgekpiresults[0];
                $key_2=$priviledge->pivot->key.'1';
                $kpiresult[$key_2]=$priviledge->pivot->value;
            }
        }
        return $kpiresult;
    }

    public function getFromCarbon(Carbon $carbon){
        $d=KPIHeader::select('id')->where('employee_id',$this->kpiheader->employee_id)
        ->where('period',$carbon)->first();

        if($d){
            $d=self::where('kpi_header_id',$d->id)->where('kpi_result_id',$this->kpi_result_id)->first();
            return $d;
        }
        else{
            return null;
        }
    }

    public function getPrev(){
        $kpiheader=$this->kpiheader;
        $period=$kpiheader->period;

        $carbon_p=Carbon::parse($period);
        $prev_p=$carbon_p->addMonth(-1);

        return $this->getFromCarbon($prev_p);
    }

    public function getNext(){
        $kpiheader=$this->kpiheader;
        $period=$kpiheader->period;

        $carbon_p=Carbon::parse($period);
        $next_p=$carbon_p->addMonth();

        return $this->getFromCarbon($next_p);
    }
}
