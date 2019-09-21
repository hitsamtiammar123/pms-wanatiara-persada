<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Support\Carbon;
use App\Model\Traits\Indexable;

/**
 *
 * @author Hitsam Tiammar <hitsamtiammmar@gmail.com>
 */

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
                $r['pw_1']=intval($kpiresultheaderprev->pw).'';
                $r['pw_2']=intval($kpiresultheader->pw).'';
                $r['pt_t1']=$kpiresultheaderprev->pt_t;
                $r['pt_k1']=$kpiresultheaderprev->pt_k;
                $r['pt_t2']=$kpiresultheader->pt_t;
                $r['pt_k2']=$kpiresultheader->pt_k;
                $r['real_t1']=$kpiresultheaderprev->real_t;
                $r['real_k1']=$kpiresultheaderprev->real_k;
                $r['real_t2']=$kpiresultheader->real_t;
                $r['real_k2']=$kpiresultheader->real_k;

                $r=$kpiresultheader->fetchFrontEndPriviledge($r,$kpiresultheaderprev);

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
                $r['pw_1']=intval($curr_p->pivot->pw).'';
                $r['pw_2']=intval($curr_s->pivot->pw).'';
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
                foreach(KPIResultHeader::KPIRESULTDKEY as $key => $index){
                    switch($unit){
                        case '$':
                        case 'WMT':
                            $curr[$key]=number_format($curr[$key]);
                        break;
                        case '%':
                        case 'MV':
                            $curr[$key]=$curr[$key].'%';
                        break;
                        case 'kwh':
                            switch($index){
                                case 'pt_t':
                                case 'pt_k':
                                    $curr[$key]='根据需要 Sesuai kebutuhan';
                                break;
                                default:
                                    $curr[$key]=number_format($curr[$key]);
                                break;
                            }
                        break;
                    }
                }

            }

        }


        return $result;
    }

    protected function getKPIA($d,$j){
        $unit=$d['unit'];
        $r=0;
        $rC=0;
        $tC=0;
        $pt_key='pt_t'.($j+1);
        $real_key='real_t'.($j+1);
        $real_k_key='real_k'.($j+1);
        $pt_k_key='pt_k'.($j+1);
        switch($unit){
            case '$':

                $rC=$d[$real_k_key];
                $tC=$d[$pt_k_key];
                break;
            default:
                $rC=$d[$real_key];
                $tC=$d[$pt_key];
            break;
        }
        if($tC!=0)
            $r=(floatval($rC)/floatval($tC))*100;
        else
            $r=0;
        return $r;
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

                if(!array_key_exists($kpia_key,$d))
                    $rt=$this->getKPIA($d,$i);
                else
                    $rt=$d[$kpia_key];

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

    protected function filterKPIResultByUnit(&$kpiresult){
        if(!array_key_exists('unit',$kpiresult))
            return;
        $unit=$kpiresult['unit'];
        $keys=array_keys($kpiresult);
        switch($unit){
            case '$':
            case 'WMT':
            case 'MT':
                if(in_array(['pt_k1','pt_t2','pt_k2'],$keys)){
                    $pt_k1=$kpiresult['pt_k1'];
                    $pt_t2=$kpiresult['pt_t2'];

                    $kpiresult['pt_k2']=intval($pt_k1+$pt_t2).'';
                }

                if(in_array(['real_k1','real_t2','real_k2'],$keys)){
                    $real_k1=$kpiresult['real_k1'];
                    $real_t2=$kpiresult['real_t2'];

                    $kpiresult['real_k2']=intval($real_k1+$real_t2).'';
                }

            break;
        }
    }

    protected function applyCreatedKPIResultFromArray($kpiresults,KPIHeader $header_prev,array &$createdlist){
        foreach($kpiresults as $k){
            if(!$header_prev)
                return;
            $new_result_id=null;
            $name=$k['name'];
            $unit=$k['unit'];

            $curr_result_prev=$header_prev->findByName($name);
            $curr_result_id='';
            $curr_result_prev_id='';
            if(!$curr_result_prev){
                $new_result=new KPIResult();
                $new_result_id=KPIResult::generateID($this->employee->id);
                $new_result->id=$new_result_id;
                $new_result->name=$name;
                $new_result->unit=$unit;
                $new_result->save();

                $curr_result_prev=new KPIResultHeader();
                $curr_result_prev_id=KPIResultHeader::generateID($this->employee->id,$header_prev->id);
                $curr_result_prev->id=$curr_result_prev_id;
                $curr_result_prev->kpi_header_id=$header_prev->id;
                $curr_result_prev->kpi_result_id=$new_result_id;

            }
            else{
                $new_result_id=$curr_result_prev->kpi_result_id;
                $curr_result_prev_id=$curr_result_prev->id;
            }

            $curr_result=new KPIResultHeader();
            $curr_result_id=KPIResultHeader::generateID($this->employee->id,$this->id);
            $curr_result->id=$curr_result_id;
            $curr_result->kpi_header_id=$this->id;
            $curr_result->kpi_result_id=$new_result_id;
            $createdlist[$curr_result_id]=$k;
            $curr_result->saveFromArray($k,$curr_result_prev,[$curr_result_prev_id,$curr_result_id]);
        }
    }

    protected function applyUpdateKPIResultFromArray($kpiresult,$header_prev,array &$updatedlist){

        if(!is_null($kpiresult['id'])){
            $curr_result=KPIResultHeader::find($kpiresult['id']);
            $curr_result_prev=$curr_result->getPrev();
            if(!is_null($curr_result) && !is_null($curr_result_prev)){
                $curr_result->setUpdatedList($kpiresult, $updatedlist);
                $curr_result->saveFromArray($kpiresult,$curr_result_prev);
            }
        }

    }

    protected function applyUpdateKPIProcessFromArray($kpiprocess,$kpiprocessdeletelist,&$kpiprocess_save,&$kpiprocess_save_n){
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

    /**
     * berfungsi untuk mengambil data kpiheader untuk dikonsumsi oleh front end
     *
     * @param string $id ID dari karyawam
     * @param string|Carbon\Carbon $curr_date tanggal dari suatu header
     * @return App\Model\KPIHeader
     */
    public static function findForFrontEnd($id,$curr_date){
        return KPIHeader::where('employee_id',$id)->where('period',$curr_date)->first();
    }

    public function getFinalAchivement(array $kpiresults,array $kpiproceses){

        $t1_fr=floatval($kpiresults['totalAchievement']['t1']);
        $t1_fp=floatval($kpiproceses['totalAchievement']['t1']);
        $t2_fr=floatval($kpiresults['totalAchievement']['t2']);
        $t2_fp=floatval($kpiproceses['totalAchievement']['t2']);

        $final_achievements=[];

        $final_achievements['t1_n']=round($t1_fr * $this->weight_result + $t1_fp * $this->weight_process,1);
        $final_achievements['t2_n']=round($t2_fr * $this->weight_result + $t2_fp * $this->weight_process,1);

        $final_achievements['t1_i']=$this->getIndexAchievement($final_achievements['t1_n']);
        $final_achievements['t2_i']=$this->getIndexAchievement($final_achievements['t2_n']);

        $final_achievements['t1_f']=round($final_achievements['t1_n']-100,1);
        $final_achievements['t2_f']=round($final_achievements['t2_n']-100,1);

        return $final_achievements;
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
                    $period=$this->cPrevPeriod();
                }
                else if($c>2 && $c<=4){
                    $period=$this->cPeriod();
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
                    $period=$this->cPrevPeriod();
                }
                else if($c>2 && $c<=4){
                    $period=$this->cPeriod();
                }

                $h.=$period->format('M');
            }
            else{
                if($i%2===0){
                    $period=$this->cPrevPeriod();
                }
                else
                    $period=$this->cPeriod();
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
                $period=$this->cPrevPeriod();
            }
            else
                $period=$this->cPeriod();

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

    /**
     *  Berfungsi untuk mengambil data KPIHeader yang akan dipakai oleh frontEnd
     *
     * @uses self::fetchKPIProcess() bacoba
     * @param string $type
     * Tipe data yang mau diambil bisa berupa kpiresult,kpiprocess,atau kpiendorsement
     * @return array
     */
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

    public function findByName($name){

        $kpiheaderresults=$this->kpiresultheaders->sortBy('created_at');

        foreach($kpiheaderresults as $headerresult){
            if(str_name_compare(trim($headerresult->kpiresult->name),trim($name)))
                return $headerresult;
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

    public function updateKPIResultFromArray($kpiresults,array &$updatedlists=[],array &$createdlists=[]){
        $header_prev=$this->getPrev();
        $keys=KPIResultHeader::numberKeys();
        //$kpiresults_collection=collect($kpiresults)->unique('name');
        if(array_key_exists('updated',$kpiresults)){
            foreach($kpiresults['updated'] as $kpiresult){

                $kpiresult=filter_is_number($kpiresult,$keys);
                $this->filterKPIResultByUnit($kpiresult);
                $this->applyUpdateKPIResultFromArray($kpiresult,$header_prev,$updatedlists,$createdlists);
            }
        }
        !array_key_exists('created',$kpiresults)?:
        $this->applyCreatedKPIResultFromArray($kpiresults['created'],$header_prev,$createdlists);
    }

    public function updateKPIProcessFromArray($kpiprocesses,$kpiprocessdeletelist){
        $kpiprocess_save=[];
        $kpiprocess_save_n=[];
        $header_prev=$this->getPrev();

        foreach($kpiprocesses as $kpiprocess){
            $this->applyUpdateKPIProcessFromArray(
                $kpiprocess,
                $kpiprocessdeletelist,
                $kpiprocess_save,
                $kpiprocess_save_n
            );
        }

        $header_prev->kpiprocesses()->sync($kpiprocess_save);
        $this->kpiprocesses()->sync($kpiprocess_save_n);
    }

    /**
     * Menghapus semua KPIResultHeader
     *
     * @return void
     */
    public function deleteKPIResulHeader(){
        foreach($this->kpiresultheaders as $resultheader){
            $resultheader->delete();
        }
    }

    /**
     * Membuat KPIEndorsement Baru
     *
     * @return int
     */
    public function makeEndorsement($_id=null){
        $employeeList=$this->employee->getHirarcialEmployee();
        $count=0;

        foreach($employeeList as $index => $employee){
            $p=$this->kpiendorsements->where('employee_id',$employee->id)->first();
            if(is_null($p)){
                $id=$this->id;
                $endorsementID=KPIEndorsement::generateID($this->employee->id);
                if(!$id){
                    if(!is_null($_id)){
                        KPIEndorsement::create([
                            'id'=>$endorsementID,
                            'kpi_header_id' =>$_id,
                            'level' =>($index+1),
                            'verified' => false,
                            'employee_id' => $employee->id
                        ]);
                        $count++;
                    }

                }
                else{
                    $this->kpiendorsements()->create([
                        'id'=>$endorsementID,
                        'level' =>($index+1),
                        'verified' => false,
                        'employee_id' => $employee->id
                    ]);
                    $count++;
                }
            }
        }
        return $count;

    }

    public function cPrevPeriod(){
        $period=$this->period;
        $date=Carbon::parse($period);
        return $date->subMonth();
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

    public function updateWeighting(array $weighting){
        $this->weight_result=$weighting['weight_result'];
        $this->weight_process=$weighting['weight_process'];

        $this->save();
    }

    public function hasFullEndorse(){
        foreach($this->kpiendorsements as $endorse){
            if(!$endorse->verified)
                return false;
        }
        return true;
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
