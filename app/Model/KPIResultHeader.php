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
    protected $fillable=['id','pw','pt_t','pt_k','real_t','real_k','kpi_header_id','kpi_result_header','kpi_result_id'];
    protected $kpiresultdkey=[];

    const FRONT_END_PROPERTY=['pw_1','pw_2','pt_t1','pt_k1','pt_t2','pt_k2','real_t1','real_k1','real_t2','real_k2'];
    const KPIRESULTDKEY=[
        'pt_t1' =>'pt_t',
        'pt_k1' => 'pt_k',
        'pt_t2' => 'pt_t',
        'pt_k2' => 'pt_k',
        'real_t1' => 'real_t',
        'real_k1' => 'real_k',
        'real_t2' => 'real_t',
        'real_k2' => 'real_k',
        'pt_t' => 'pt_t',
        'real_t' => 'real_t'
    ];
    const KPIRESULTPWKEY=[
        'pw_1'=>'pw',
        'pw_2'=>'pw'
    ];

    const KPIRESULTPREVKEY=[
        'pw_1',
        'pt_t1',
        'pt_k1',
        'real_t1',
        'real_k1'
    ];

    const KPIRESULTCURRENTKEY=[
        'pw_2',
        'pt_t2',
        'pt_k2',
        'real_t2',
        'real_k2'
    ];

    const KPIRESULTKPIAKEY=[
        'kpia_1',
        'kpia_2'
    ];

    const KPIRESULTORIGINALKEY=[
        'pw',
        'pt_t',
        'pt_k',
        'real_t',
        'real_k'
    ];

    const KPIRESULTFRONTKEY=[
        'name' => 'name',
        'unit' =>'unit'
    ];

    /**
     * Mapping data KPIResultHeader dari array
     *
     * @param array $mapping
     * data mapping. menerima mapping dari KPIResulHeader::KPIRESULTCURRENTKEY atau KPIResultHeader::KPIRESULTPREVKEY
     * @param array|Illuminate\Support\Collection $kpiresult
     * Data KPIresultHeader yang mau dimasukan
     * @return void
     */
    public function mapFromArr(array $mapping,$kpiresult){
        $mapping=array_merge(
            $mapping,
            array_keys(self::KPIRESULTPWKEY)
        );
        $kpiresultdkey=$this->kpiresultdkey;
        foreach($kpiresult as $key => $value){
            if(in_array($key,$mapping)){
                $map=$kpiresultdkey[$key];
                $this->{$map}=$value;
            }
        }
    }

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
            $curr_delete_prev=$curr_delete->getPrev();
            if($curr_delete && !$curr_delete->kpiheader->employee->hasTags() ){
                //is_null($curr_delete_prev)?:$curr_delete_prev->delete();

                if(!is_null($curr_delete_prev) && $curr_delete_prev->getPrev()===null){
                    $curr_delete_prev->delete();
                }
                $curr_delete->kpiresult->kpiresultheaders->count()!==0?:$curr_delete->kpiresult->delete();
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

    public static function numberKeys(){
        return array_merge(
            static::KPIRESULTCURRENTKEY,
            static::KPIRESULTPREVKEY,
            static::KPIRESULTKPIAKEY,
            array_values(static::KPIRESULTKPIAKEY)
        );
    }

    public static function updateResultHeaderFromArr(array $kpiresults,$numberkeys){
        foreach($kpiresults as $key2 =>$kpiresultheader){
            $kpiresultheaderObj=static::find($key2);
            $kpiresultheader=filter_is_number($kpiresultheader,$numberkeys);
            $kpiresultheaderObj->mapFromArr(static::KPIRESULTORIGINALKEY,$kpiresultheader);
            $kpiresultheaderObj->save();
        }
    }

        /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {

        $this->kpiresultdkey=array_merge(KPIResultHeader::KPIRESULTPWKEY,KPIResultHeader::KPIRESULTDKEY);
        parent::__construct($attributes);

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
        if(($unit==='#' || $unit === 'kwh') && $pt_t==0)
            return true;
        else
            return false;

    }

    /**
     * Melakukan mapping jika data ini memiliki priviledge
     *
     * @param string|null $value Nilai yang mau dimasukan
     * @param string|null $id ID dari KPIResult yang bersangkutan
     * @return void
     */
    public function mapPriviledge($value=null,$id=null){
        if($this->isPriviledge()){
            $nvalue=intval($value);
            if(is_null($this->priviledge)){
                if(is_null($id)){
                    $this->priviledge()->create([
                        'value'=>$nvalue
                    ]);
                }
                else{
                    PriviledgeKPIResult::create([
                        'kpi_header_result_id' =>$id,
                        'value'=>$nvalue
                    ]);
                }
            }
            else{
                $p=$this->priviledge;
                $p->value=$nvalue;
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
    public function fetchFrontEndPriviledge(array &$kpiresult,KPIResultHeader $prev){
        if($this->isPriviledge()){
            if(!is_null($this->priviledge) && !is_null($prev->priviledge)){
                $kpiresult['kpia_1']=$prev->priviledge->value;
                $kpiresult['kpia_2']=$this->priviledge->value;
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

    /**
     *
     * @param array|Illuminate\Support\Collection $kpiresult Data Array dari kpiresult yang mau di-simpan
     * @param App\Model\KPIResultHeader $_prev Data KPIResultHeader pada periode sebelumnya
     * @return void
     */
    public function saveFromArray($kpiresult, KPIResultHeader $_prev=null,$ids=[]){
        $result_prev=!is_null($_prev)?$_prev:$this->getPrev();

        if(!$this->kpiheader->employee->hasTags()){
            array_key_exists('name',$kpiresult)?$this->kpiresult->name=$kpiresult['name']:null;
            array_key_exists('unit',$kpiresult)?$this->kpiresult->unit=$kpiresult['unit']:null;
        }

        if(array_key_exists('hasNew',$kpiresult) && array_key_exists('hasPrev',$kpiresult) &&
        $kpiresult['hasNew']===true && $kpiresult['hasPrev']===false){
            $result_prev->mapFromArr(KPIResultHeader::KPIRESULTPREVKEY,$kpiresult);
            $result_prev->save();

            $id=array_key_exists(0,$ids)?$ids[0]:null;
            $kpia_1=array_key_exists('kpia_1',$kpiresult)?$kpiresult['kpia_1']:null;
            $result_prev->mapPriviledge($kpia_1,$id);
        }


        $this->mapFromArr(KPIResultHeader::KPIRESULTCURRENTKEY,$kpiresult);
        $this->push();

        $id=array_key_exists(1,$ids)?$ids[1]:null;
        $kpia_2=array_key_exists('kpia_2',$kpiresult)?$kpiresult['kpia_2']:null;
        $this->mapPriviledge($kpia_2,$id);

    }

    /**
     * Melakukan mapping untuk data-data yang diubah
     *
     * @param mixed $kpiresult
     * @param array &$updatedlist
     * @return void
     */

    public function setUpdatedList($kpiresult, array &$updatedlist){

        $keys=array_merge(static::KPIRESULTDKEY,static::KPIRESULTFRONTKEY);
        $total=[];
        $char_set=['name','unit'];
        foreach($kpiresult as $key =>$value){
            if(
                in_array($key,array_keys($keys))
            ){
                $arr=[];
                $map=$keys[$key];
                $arr['oldval']=in_array($key,$char_set)?$this->kpiresult->{$map}:$this->{$map};
                $arr['newval']=$value;
                if(in_array($key,$char_set)){
                    if(trim($arr['oldval'])!==trim($arr['newval']))
                        $total[$map]=$arr;
                }
                else{
                    if(round(floatval($arr['oldval']),2)!==round(floatval($arr['newval']),2))
                        $total[$map]=$arr;
                }

            }
        }
        $updatedlist[$kpiresult['id']]=$total;

        $result_prev=$this->getPrev();
        if(array_key_exists('kpia_1',$kpiresult) && !is_null($result_prev)){
            $result_prev->setUpdatedPriviledgeList($updatedlist,$kpiresult['kpia_1'],'kpia_1');
        }

        if(array_key_exists('kpia_2',$kpiresult)){
            $this->setUpdatedPriviledgeList($updatedlist,$kpiresult['kpia_2'],'kpia_2');
        }
    }

    public function setAsNewData($flag=false){
        $unit=$this->kpiresult->unit;
        $prev=$this->getPrev();


        switch($unit){
            case '$':
            case 'WMT':
            case 'MT':
            if($prev){
                $pt_k1=$prev->pt_k;
                $pt_t2=$this->pt_t;

                $this->pt_k=intval($pt_k1)+intval($pt_t2);

                $real_k1=$prev->real_k;
                $real_t2=$this->real_t;

                $this->real_k=intval($real_k1)+intval($real_t2);
                $this->pt_t=$flag===true?$this->pt_t:0;
                $this->real_t=$flag===true?$this->real_t:0;

                break;
            }
            default:
                if($flag===true)
                    break;

                $this->pt_t=0;
                $this->pt_k=0;
                $this->real_t=0;
                $this->real_k=0;
            break;
        }
    }

        /**
     * Melakukan mapping untuk data KPIResult Priviledge
     *
     * @param array &$updatedlist
     * @param mixed $value
     * @param string $key
     * @return void
     */
    public function setUpdatedPriviledgeList(array &$updatedlist,$value=null,$key='kpia_1'){
        if($this->isPriviledge()){
            $oldvalue=$this->priviledge->value;
            if(!array_key_exists($this->id,$updatedlist)){
                $updatedlist[$this->id]=[];
            }

            $arr=[
                'oldvalue' => $oldvalue,
                'newvalue' =>$value
            ];
            if(intval($oldvalue)!==intval($value))
                $updatedlist[$this->id][$key]=$arr;
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
