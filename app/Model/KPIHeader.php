<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Support\Carbon;
use App\Model\Traits\Indexable;

class KPIHeader extends Model
{
    //
    use DynamicID,Indexable;

    protected static $listID=[];
    protected $table='kpiheaders';
    protected $fillable=[
        'period'
    ];

    protected $casts=['id'=>'string'];

    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at'];

    protected function fetchKPIResult(){

        $result=[];
        $kpi_results_header_start=$this->kpiresultheaders->sortBy('created_at');

        foreach($kpi_results_header_start as $kpiresultheader){
            $r=[];
            $kpiresult=KPIResult::find($kpiresultheader->kpi_result_id);
            $kpiresultheaderend=$kpiresultheader->getNext();

            if($kpiresultheaderend){
                $r['kpi_header_id']=$this->id;
                $r['kpi_result_id']=$kpiresultheader->kpi_result_id;
                $r['name']=$kpiresult->name;
                $r['unit']=$kpiresult->unit;

                $r['id']=$kpiresultheader->id;
                $r['pw_1']=$kpiresultheader->pw;
                $r['pw_2']=$kpiresultheaderend->pw;
                $r['pt_t1']=$kpiresultheader->pt_t;
                $r['pt_k1']=$kpiresultheader->pt_k;
                $r['pt_t2']=$kpiresultheaderend->pt_t;
                $r['pt_k2']=$kpiresultheaderend->pt_k;
                $r['real_t1']=$kpiresultheader->real_t;
                $r['real_k1']=$kpiresultheader->real_k;
                $r['real_t2']=$kpiresultheaderend->real_t;
                $r['real_k2']=$kpiresultheaderend->real_k;

                $result[]=$r;
            }

        }

        return $result;
    }

    protected function fetchKPIProcess(){
        $result=[];
        $kpi_proccess_start=$this->kpiprocesses;

        foreach($kpi_proccess_start as $curr_s){
            $r=[];
            $curr_e=$curr_s->getNext();

            if($curr_e){
                $r['id']=$curr_s->id;
                $r['name']=$curr_s->name;
                $r['unit']=$curr_s->unit;
                $r['pw_1']=$curr_s->pivot->pw;
                $r['pw_2']=$curr_e->pivot->pw;
                $r['pt_1']=$curr_s->pivot->pt;
                $r['pt_2']=$curr_e->pivot->pt;
                $r['real_1']=$curr_s->pivot->real;
                $r['real_2']=$curr_e->pivot->real;
                $result[]=$r;
            }

        }
        return $result;
    }

    protected function sumTotalAchievement($data,$j){
        $s=0;
        $aw_key='aw_';
        for($i=0;$i<count($data);$i++){
            $d=$data[$i];
            $curr_index=$aw_key.($j+1);
            $aw=$d[$curr_index];
            $n=floatval($aw);
            $s+=$n;
        }
        return $s;
    }

    protected function getIndexAchievement($s){
        $index='';
        if($s<80){
            $index="D";
        }
        else if($s>=80 && $s<82){
            $index="C";
        }
        else if($s>=82 && $s<85){
            $index="C+";
        }
        else if($s>=85 && $s<90){
            $index="B-";
        }
        else if($s>=90 && $s<95){
            $index="B";
        }
        else if($s>=95 && $s<100){
            $index="B+";
        }
        else if($s>=100 && $s<102){
            $index="A-";
        }
        else if($s>=102 && $s<105){
            $index="A";
        }
        else if($s>=105){
            $index="A+";
        }

        return $index;
    }

    protected function getKPIProcessColor($r){
        if($r<0)
            return 'black-column';
        else if($r===0)
            return 'green-column';
        else if($r===1)
            return 'blue-column';
        else if($r>1)
            return 'gold-column';
        else
            return '';
    }

    protected function getKPIProcessIndex($r){
        if($r<0)
            return 80;
        else if($r===0)
            return 100;
        else if($r===1)
            return 110;
        else if($r>1)
            return 120;
        else
            return 0;

    }

    protected function accumulateTotalAchievement($result){
        $totalAchivement=[];
        $indexAchivement=[];
        for($i=0;$i<2;$i++){
            $t='t'.($i+1);
            $s=$this->sumTotalAchievement($result,$i);

            $totalAchivement[$t]=round($s,1).'%';

            $index=$this->getIndexAchievement($s);
            $indexAchivement[$t]=$index;
        }

        return [
            'totalAchievement'=>$totalAchivement,
            'indexAchivement'=>$indexAchivement
        ];
    }

    protected function fetchAccumulatedKPIResult(){
        $kpiresults=$this->fetchKPIResult();
        $result=[];
        $totalAchivement=[];
        $indexAchivement=[];

        foreach($kpiresults as $d){
            for($i=0;$i<2;$i++){
                $kpia_key='kpia_'.($i+1);
                $aw_key='aw_'.($i+1);
                $pt_key='pt_t'.($i+1);
                $real_key='real_t'.($i+1);
                $pwq_key='pw_'.($i+1);

                if($d[$pt_key]!=0){
                    $rt=$d[$real_key]/$d[$pt_key]*100;
                }
                else
                    $rt=0;

                $rt=round($rt,1);
                $bColor='bColor_kpia_'.($i+1);

                if($rt>=120){
                    $d[$bColor]='gold-column';
                }
                else if($rt>=105 && $rt<120){
                    $d[$bColor]='blue-column';
                }
                else if($rt>=95 && $rt<105){
                    $d[$bColor]='green-column';
                }
                else if($rt<95){
                    $d[$bColor]='red-column';
                }

                $d[$kpia_key]=$rt;
                $pwq=$d[$pwq_key];
                $calculate=$rt*$pwq/100;
                $d[$aw_key]=round($calculate,1);
            }
            $result[]=$d;

        }

        $accumulated=$this->accumulateTotalAchievement($result);

        return [
            'data'=>$result,
            'totalAchievement'=>$accumulated['totalAchievement'],
            'indexAchievement'=>$accumulated['indexAchivement']
        ];
    }

    protected function fetchAccumulatedKPIProcess(){

        $kpiprocesses=$this->fetchKPIProcess();
        $result=[];

        foreach($kpiprocesses as $curr){

            $kt_1=intval($curr['real_1'])-intval($curr['pt_1']);
            $kt_2=intval($curr['real_2'])-intval($curr['pt_2']);
            $curr['kpia_1']=$this->getKPIProcessIndex($kt_1);
            $curr['kpia_2']=$this->getKPIProcessIndex($kt_2);
            $curr['bColor_kpia_1']=$this->getKPIProcessColor($kt_1);
            $curr['bColor_kpia_2']=$this->getKPIProcessColor($kt_2);

            $curr['aw_1']=round(($curr['kpia_1']/100)*intval($curr['pw_1']),1);
            $curr['aw_2']=round(($curr['kpia_2']/100)*intval($curr['pw_2']),1);

            $result[]=$curr;
        }

        $accumulated=$this->accumulateTotalAchievement($result);

        return [
            'data'=>$result,
            'totalAchievement'=>$accumulated['totalAchievement'],
            'indexAchievement'=>$accumulated['indexAchivement']
        ];
    }

    public static function generateID($employeeID){
        $employee=Employee::find($employeeID);

        if(!$employee){
            return null;
        }

        $employee_index=$employee->getIndex();
        $header_count=$employee->kpiheaders()->count();

        $a=4;
        $code=add_zero($employee_index,1).add_zero($header_count,1);

        return self::_generateID($a,$code);

    }

    public static function getDate($month){
        $curr=Carbon::now();
        $year=$curr->year;
        $month=$month;
        $date=16;
        $curr_date=Carbon::createFromDate($year,$month,$date)->format('Y-m-d');
        return $curr_date;
    }

    public static function getCurrentDate(){
        $curr_date=self::getDate(Carbon::now()->month);
        return $curr_date;

    }

    public function fetchFrontEndData($type){

        if($type==='kpiresult'){
            return $this->fetchKPIResult();
        }
        else if($type==='kpiprocess'){
            return $this->fetchKPIProcess();
        }
        return null;


    }

    public function fetchAccumulatedData($type){
        if($type==='kpiresult'){
            return $this->fetchAccumulatedKPIResult();
        }
        else if($type==='kpiprocess'){
            return $this->fetchAccumulatedKPIProcess();
        }
        return null;
    }

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function kpiendorsements(){
        return $this->hasMany(KPIEndorsement::class,'kpi_header_id','id');
    }

    public function kpiresultheaders(){
        return $this->hasMany(KPIResultHeader::class,'kpi_header_id','id');
    }

    public function kpiprocesses(){
        return $this->belongsToMany(KPIProcess::class,'kpiprocesses_kpiheaders','kpi_header_id','kpi_proccess_id')
        ->withTimestamps()->withPivot(['pw','pt','real']);
    }

    public function getNext(){
        $date=Carbon::parse($this->period);
        $next_date=$date->addMonth();

        $r=self::where('period',$next_date)->where('employee_id',$this->employee_id)->first();

        return $r;
    }
}
