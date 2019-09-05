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
        'id','period','employee_id','weight_result','weight_process'
    ];
    protected $hidden=[
        'created_at','updated_at','deleted_at'
    ];

    protected $casts=['id'=>'string'];
    protected $kpiResultDKeys=[
        'pt_t1' =>'pt_t',
        'pt_k1' => 'pt_k',
        'pt_t2' => 'pt_t',
        'pt_k2' => 'pt_k',
        'real_t1' => 'real_t',
        'real_k1' => 'real_k',
        'real_t2' => 'real_t',
        'real_k2' => 'real_k'
    ];

    const HIDDEN_PROPERTY=['created_at','updated_at','deleted_at'];



    protected function fetchKPIResult(){

        $result=[];
        $kpi_results_header_start=$this->kpiresultheaders->sortBy('kpiresult.name');

        foreach($kpi_results_header_start as $kpiresultheader){
            $r=[];
            $kpiresult=KPIResult::find($kpiresultheader->kpi_result_id);
            $kpiresultheaderend=$kpiresultheader->getNext();
            $kpiresultheaderprev=$kpiresultheader->getPrev();

            if($kpiresultheaderprev){
                $r['kpi_header_id']=$this->id;
                $r['kpi_result_id']=$kpiresultheader->kpi_result_id;
                $r['name']=$kpiresult->name;
                $r['unit']=$kpiresult->unit;

                $r['id']=$kpiresultheader->id;
                $r['pw_1']=$kpiresultheaderprev->pw;
                $r['pw_2']=$kpiresultheader->pw;
                $r['pt_t1']=$kpiresultheaderprev->pt_t;
                $r['pt_k1']=$kpiresultheaderprev->pt_k;
                $r['pt_t2']=$kpiresultheader->pt_t;
                $r['pt_k2']=$kpiresultheader->pt_k;
                $r['real_t1']=$kpiresultheaderprev->real_t;
                $r['real_k1']=$kpiresultheaderprev->real_k;
                $r['real_t2']=$kpiresultheader->real_t;
                $r['real_k2']=$kpiresultheader->real_k;

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
            $curr_p=$curr_s->getPrev();

            if($curr_p){
                $r['id']=$curr_s->id;
                $r['name']=$curr_s->name;
                $r['unit']=$curr_s->unit;
                $r['pw_1']=$curr_p->pivot->pw;
                $r['pw_2']=$curr_s->pivot->pw;
                $r['pt_1']=$curr_p->pivot->pt;
                $r['pt_2']=$curr_s->pivot->pt;
                $r['real_1']=$curr_p->pivot->real;
                $r['real_2']=$curr_s->pivot->real;
                $result[]=$r;
            }

        }
        return $result;
    }

    protected function fetchKPIEndorsement(){
        $endorsements=$this->kpiendorsements;
        $endorsements->each(function($data,$key){
            $data->load('employee');
            $data->makeHidden(KPIEndorsement::HIDDEN_PROPERTY);
            $data->employee->makeHidden(Employee::HIDDEN_PROPERTY);
            $data->employee->load('role');
            $data->employee->role->makeHidden(Role::HIDDEN_PROPERTY);
        });

        return $endorsements->keyBy('level');
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

    protected function filterData($result,$type){
        for($i=0;$i<count($result);$i++){
            $curr=&$result[$i];
            $curr['pw_1']=$curr['pw_1'].'%';
            $curr['pw_2']=$curr['pw_2'].'%';
            $curr['kpia_1']=$curr['kpia_1'].'%';
            $curr['kpia_2']=$curr['kpia_2'].'%';
            $curr['aw_1']=$curr['aw_1'].'%';
            $curr['aw_2']=$curr['aw_2'].'%';

            if($type==='kpiresult'){
                $unit=$curr['unit'];
                foreach(array_keys($this->kpiResultDKeys) as $key){
                    switch($unit){
                        case '$':
                        case 'WMT':
                            $curr[$key]=number_format($curr[$key]);
                        break;
                        case '%':
                        case 'MV':
                            $curr[$key]=$curr[$key].'%';
                        break;
                    }
                }

            }

        }


        return $result;
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
        $result=$this->filterData($result,'kpiresult');
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

            if(!is_null($curr['real_1'])&&!is_null($curr['pt_1']))
                $kt_1=intval($curr['real_1'])-intval($curr['pt_1']);
            else
                $kt_1=-1;

            if(!is_null($curr['real_2'])&&!is_null($curr['pt_2']))
                $kt_2=intval($curr['real_2'])-intval($curr['pt_2']);
            else
                $kt_2=-1;

            $curr['kpia_1']=$this->getKPIProcessIndex($kt_1);
            $curr['kpia_2']=$this->getKPIProcessIndex($kt_2);
            $curr['bColor_kpia_1']=$this->getKPIProcessColor($kt_1);
            $curr['bColor_kpia_2']=$this->getKPIProcessColor($kt_2);

            $curr['aw_1']=round(($curr['kpia_1']/100)*intval($curr['pw_1']),1);
            $curr['aw_2']=round(($curr['kpia_2']/100)*intval($curr['pw_2']),1);

            $result[]=$curr;
        }

        $accumulated=$this->accumulateTotalAchievement($result);
        $result=$this->filterData($result,'kpiprocess');

        return [
            'data'=>$result,
            'totalAchievement'=>$accumulated['totalAchievement'],
            'indexAchievement'=>$accumulated['indexAchivement']
        ];
    }

    protected function applyUpdateKPIResultFromArray($kpiresult,$header_prev){

        if(!is_null($kpiresult['id'])){
            $curr_result=KPIResultHeader::find($kpiresult['id']);
            $curr_result_prev=$curr_result->getPrev();
            if($curr_result_prev){
                $curr_result_prev->pw=$kpiresult['pw_1'];
                $curr_result_prev->pt_t=$kpiresult['pt_t1'];
                $curr_result_prev->pt_k=$kpiresult['pt_k1'];
                $curr_result_prev->real_t=$kpiresult['real_t1'];
                $curr_result_prev->real_k=$kpiresult['real_k1'];

                $curr_result->pw=$kpiresult['pw_2'];
                $curr_result->pt_t=$kpiresult['pt_t2'];
                $curr_result->pt_k=$kpiresult['pt_k2'];
                $curr_result->real_t=$kpiresult['real_t2'];
                $curr_result->real_k=$kpiresult['real_k2'];

                $curr_result->kpiresult->name=$kpiresult['name'];
                $curr_result->kpiresult->unit=$kpiresult['unit'];

                $curr_result->push();
                $curr_result_prev->save();
            }
        }
        else{
            if(!$header_prev)
                return;

            $curr_result=new KPIResultHeader();
            $curr_result->id=KPIResultHeader::generateID($this->employee->id,$this->id);
            $curr_result->kpi_header_id=$this->id;

            $curr_result_prev=new KPIResultHeader();
            $curr_result_prev->id=KPIResultHeader::generateID($this->employee->id,$header_prev->id);
            $curr_result_prev->kpi_header_id=$header_prev->id;

            $new_result=new KPIResult();
            $new_result_id=KPIResult::generateID($this->employee->id);
            $new_result->id=$new_result_id;
            $new_result->name=$kpiresult['name'];
            $new_result->unit=$kpiresult['unit'];

            $curr_result->kpi_result_id=$new_result_id;
            $curr_result_prev->kpi_result_id=$new_result_id;

            $curr_result_prev->pw=$kpiresult['pw_1'];
            $curr_result_prev->pt_t=$kpiresult['pt_t1'];
            $curr_result_prev->pt_k=$kpiresult['pt_k1'];
            $curr_result_prev->real_t=$kpiresult['real_t1'];
            $curr_result_prev->real_k=$kpiresult['real_k1'];

            $curr_result->pw=$kpiresult['pw_2'];
            $curr_result->pt_t=$kpiresult['pt_t2'];
            $curr_result->pt_k=$kpiresult['pt_k2'];
            $curr_result->real_t=$kpiresult['real_t2'];
            $curr_result->real_k=$kpiresult['real_k2'];

            $new_result->save();
            $curr_result_prev->save();
            $curr_result->save();

        }
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

    public static function getDate($month,$year=null){
        $curr=Carbon::now();
        $year=$year?$year:$curr->year;
        $month=$month;
        $date=16;
        $curr_date=Carbon::createFromDate($year,$month,$date)->format('Y-m-d');
        return $curr_date;
    }

    public function getResultHeading(){
        $headings=[];

        for($i=0,$period=null,$c=0;$i<14;$i++){
            $h='';

            if($c>=4)
                $c=0;

            if($i>=2 && $i<=5){
                if($i%2===0){
                    $h.='Target ';
                }
                else{
                    $h.='Kumulatif ';
                }
                $c++;
                if($c<=2){
                    $period=$this->cPeriod();
                }
                else if($c>2 && $c<=4){
                    $period=$this->cNextPeriod();
                }
                $h.=$period->format('M');

            }
            else if($i>=6 && $i<=9){
                if($i%2===0){
                    $h.='Realisasi ';
                }
                else{
                    $h.='Kumulatif ';
                }
                $c++;
                if($c<=2){
                    $period=$this->cPeriod();
                }
                else if($c>2 && $c<=4){
                    $period=$this->cNextPeriod();
                }

                $h.=$period->format('M');
            }
            else{
                if($i%2===0){
                    $period=$this->cPeriod();
                }
                else
                    $period=$this->cNextPeriod();
                $h=$period->format('M');
            }

            $headings[]=$h;
        }

        return $headings;
    }

    public function getProcessHeading(){
        $headings=[];

        for($i=0,$period=null;$i<10;$i++){
            $h='';
            if($i%2===0){
                $period=$this->cPeriod();
            }
            else
                $period=$this->cNextPeriod();

            switch($i){
                case 2:
                case 3:
                    $h.='Target ';
                break;
                case 4:
                case 5:
                    $h.='Realisasi ';
                break;
            }

            $h.=$period->format('M');
            $headings[]=$h;

        }

        return $headings;
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
        else if($type==='kpiendorsement'){
            return $this->fetchKPIEndorsement();
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

    public function updateKPIResultFromArray($kpiresults){
        $header_prev=$this->getPrev();
        foreach($kpiresults as $kpiresult){
            $kpiresult=filter_is_number($kpiresult,KPIResultHeader::FRONT_END_PROPERTY);
            $this->applyUpdateKPIResultFromArray($kpiresult,$header_prev);
        }
    }

    public function updateKPIProcessFromArray($kpiprocesses,$kpiprocessdeletelist){
        $kpiprocess_save=[];
        $kpiprocess_save_n=[];
        $header_prev=$this->getPrev();

        foreach($kpiprocesses as $kpiprocess){

            $curr_process_id=$kpiprocess['id'];
            $curr_process=KPIProcess::find($curr_process_id);
            $curr_process->unit=$kpiprocess['unit'];
            $kpiprocess=filter_is_number($kpiprocess,KPIProcess::FRONT_END_PROPERTY);

            if(!in_array($curr_process_id,$kpiprocessdeletelist)){
                $kpiprocess_save[$curr_process_id]=[
                    'pw'=>$kpiprocess['pw_1'],
                    'pt'=>$kpiprocess['pt_1'],
                    'real'=>$kpiprocess['real_1']
                ];
                $kpiprocess_save_n[$curr_process_id]=[
                    'pw'=>$kpiprocess['pw_2'],
                    'pt'=>$kpiprocess['pt_2'],
                    'real'=>$kpiprocess['real_2']
                ];

                $curr_process->save();

            }
        }

        $header_prev->kpiprocesses()->sync($kpiprocess_save);
        $this->kpiprocesses()->sync($kpiprocess_save_n);
    }

    public function cPeriod(){
        $period=$this->period;
        $date=Carbon::parse($period);
        return $date;
    }

    public function cNextPeriod(){
        $period=$this->period;
        $date=Carbon::parse($period);
        return $date->addMonth();
    }

    public function cCumStartPeriod(){
        $period=$this->period;
        $date=Carbon::parse($period);
        $date->setMonth(12);
        $date->setYear($date->year-1);
        return $date;
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

    public function getHeaderByDate($date){
        return self::where('period',$date)->where('employee_id',$this->employee_id)->first();
    }

    public function getNext(){
        $date=Carbon::parse($this->period);
        $next_date=$date->addMonth();

        return $this->getHeaderByDate($next_date);
    }

    public function getPrev(){
        $date=Carbon::parse($this->period);
        $prev_date=$date->addMonth(-1);

        return $this->getHeaderByDate($prev_date);
    }

    public function getEndorse($employee){
        $endorsements=$this->kpiendorsements;

        foreach($endorsements as $endorse){
            if($endorse->employee->id===$employee->id)
                return $endorse;
        }
        return null;
    }

    public function getSelfEndorse(){
        return $this->getEndorse($this->employee);
    }
}
