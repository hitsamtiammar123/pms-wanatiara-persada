<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\Traits\DynamicID;
use Illuminate\Support\Carbon;
use App\Model\Traits\Indexable;
use App\Model\Interfaces\Endorseable;

/**
 *
 * @author Hitsam Tiammar <hitsamtiammmar@gmail.com>
 */

class KPIHeader extends Model implements Endorseable
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



    protected function fetchKPIResult($groupdata=null){

        $result=[];
        if(is_null($groupdata))
            $kpi_results_header_start=$this->kpiresultheaders->sortBy('kpiresult.name');
        else{
            $modelkey=$groupdata->modelKeys();
            $kpi_results_header_start=$this->kpiresultheaders->whereIn('kpi_result_id',$modelkey)->sortBy('kpiresult.name');
        }

        foreach($kpi_results_header_start as $kpiresultheader){
            $r=[];
            $kpiresult=KPIResult::find($kpiresultheader->kpi_result_id);
            //$kpiresultheaderend=$kpiresultheader->getNext();
            $kpiresultheaderprev=$kpiresultheader->getPrev();
            if($kpiresultheaderprev){
                $r['pw_1']=intval($kpiresultheaderprev->pw).'';
                $r['pt_t1']=$kpiresultheaderprev->pt_t;
                $r['pt_k1']=$kpiresultheaderprev->pt_k;
                $r['real_t1']=$kpiresultheaderprev->real_t;
                $r['real_k1']=$kpiresultheaderprev->real_k;
                $r=$kpiresultheader->fetchFrontEndPriviledge($r,$kpiresultheaderprev);
            }

                $r['kpi_header_id']=$this->id;
                $r['kpi_result_id']=$kpiresultheader->kpi_result_id;
                $r['name']=$kpiresult->name;
                $r['unit']=$kpiresult->unit;
                $r['id']=$kpiresultheader->id;
                $r['pw_2']=intval($kpiresultheader->pw).'';
                $r['pt_t2']=$kpiresultheader->pt_t;
                $r['pt_k2']=$kpiresultheader->pt_k;
                $r['real_t2']=$kpiresultheader->real_t;
                $r['real_k2']=$kpiresultheader->real_k;


                $result[]=$r;


        }

        return $result;
    }

    protected function fetchKPIProcess($groupdata=null){
        $result=[];
        if(is_null($groupdata))
            $kpi_proccess_start=$this->kpiprocesses;
        else{
            $modelkey=$groupdata->modelKeys();
            $kpi_proccess_start=$this->kpiprocesses->whereIn('id',$modelkey);
        }

        foreach($kpi_proccess_start as $curr_s){
            $r=[];
            $curr_e=$curr_s->getNext();
            $curr_p=$curr_s->getPrev();

            if( $curr_p){
                $r['pw_1']=intval($curr_p->pivot->pw).'';
                $r['pt_1']=$curr_p->pivot->pt;
                $r['real_1']=$curr_p->pivot->real;
            }
            $r['id']=$curr_s->id;
            $r['name']=$curr_s->name;
            $r['unit']=$curr_s->unit;
            $r['kpi_header_id']=$this->id;
            $r['pw_2']=intval($curr_s->pivot->pw).'';
            $r['pt_2']=$curr_s->pivot->pt;
            $r['real_2']=$curr_s->pivot->real;
            $result[]=$r;

        }
        return $result;
    }

    protected function fetchKPIEndorsement(){
        return KPIEndorsement::fetchFromHirarcialArr(
            $this->employee->getHirarcialEmployee(),$this
        );
    }

    protected function sumTotalAchievement($data,$j,$isgroup=false){
        $s=0;
        $aw_key='aw_';
        $kpia_key='kpia_';
        for($i=0;$i<count($data);$i++){


            $d=$data[$i];
            $n=0;
            if(!$isgroup){
                $curr_index=$aw_key.($j+1);
                $aw=array_key_exists($curr_index,$d)?$d[$curr_index]:0;;
                $n=floatval($aw);
            }
            else{
                $curr_index=$kpia_key.($j+1);
                $kpia=array_key_exists($curr_index,$d)?$d[$curr_index]:0;
                $n=floatval($kpia);
            }
            $s+=$n;
        }
        return !$isgroup?$s:(count($data)!==0?$s/count($data):0);
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

    protected function accumulateTotalAchievement($result,$isgroup=false){
        $totalAchivement=[];
        $indexAchivement=[];
        for($i=0;$i<2;$i++){
            $t='t'.($i+1);
            $s=$this->sumTotalAchievement($result,$i,$isgroup);

            $totalAchivement[$t]=round($s,2).'%';

            $index=$this->getIndexAchievement($s);
            $indexAchivement[$t]=$index;
        }

        return [
            'totalAchievement'=>$totalAchivement,
            'indexAchivement'=>$indexAchivement
        ];
    }

    protected function filterData($result,$type,$isgroup=false){
        for($i=0;$i<count($result);$i++){
            $curr=&$result[$i];

            $curr['pw_1']=@$curr['pw_1'].'%';
            $curr['kpia_1']=@$curr['kpia_1'].'%';
            $curr['aw_1']=@$curr['aw_1'].'%';

            $curr['pw_2']=$curr['pw_2'].'%';
            $curr['kpia_2']=$curr['kpia_2'].'%';
            $curr['aw_2']=$curr['aw_2'].'%';


                $unit=$curr['unit'];
            $mapping=($type==='kpiresult')?KPIResultHeader::KPIRESULTDKEY:KPIProcess::KPIPROCESSCURRKEYREVERSE;
                foreach($mapping as $key => $index){
                    if(!array_key_exists($key,$curr))
                        continue;
                    switch($unit){
                    case '$':
                    case 'MT':
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
                        case '规模 Skala':
                        case '规模 Scale':
                            if(!$isgroup)
                                break;

                            $j=intval($curr[$key]);
                            if($j<=0)
                                $curr[$key]='Sangat Buruk';
                            else if($j==1)
                                $curr[$key]='Buruk';
                            else if($j==2)
                                $curr[$key]='Sedang';
                            else if($j==3)
                                $curr[$key]='Baik';
                            else if($j>=4)
                                $curr[$key]='Sangat Baik';
                        break;
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

    protected function getKPIAByIndex($rt,$unit){
        $i=floatval($rt);
        switch($unit){
            case '规模 Skala':
            case '规模 Scale':
                if($i<=0)
                   $rt=70;
                else if($i==1)
                    $rt=80;
                else if($i==2)
                    $rt=90;
                else if($i==3)
                    $rt=100;
                else if($i>=4)
                    $rt=120;
            break;
            case 'MT':
            case 'WMT':
                if($i<=0.8)
                    $rt=80;
                else if($i>0.8 && $i<=0.9)
                    $rt=90;
                else if($i>0.9 && $i<1)
                    $rt=95;
                else if($i>=1 && $i<=1.025)
                    $rt=102;
                else if($i>1.025)
                    $rt=110;

        }
        return $rt;
    }

    protected function getKPIAForGrouping($d,$j){
        $unit=$d['unit'];
        $r=0;
        $pt_key='pt_t'.($j+1);
        $real_key='real_t'.($j+1);
        if(!array_key_exists($real_key,$d) && !array_key_exists($pt_key,$d))
            return 0;

        switch($unit){
            case 'MT':
            case 'WMT':
                $r=(floatval($d[$real_key])/floatval($d[$pt_key]));
            break;
            case '规模 Skala':
            case '规模 Scale':
                $r=$d[$real_key];
            break;
        }

        return $this->getKPIAByIndex($r,$unit);

    }

    protected function fetchAccumulatedKPIResult($groupdata=null){
        $kpiresults=$this->fetchKPIResult($groupdata);
        $result=[];
        $totalAchivement=[];
        $indexAchivement=[];

        foreach($kpiresults as $d){
            for($i=0;$i<2;$i++){
                if(!is_null($groupdata) && $i===0)
                    continue;

                $kpia_key='kpia_'.($i+1);
                $aw_key='aw_'.($i+1);
                $pt_key='pt_t'.($i+1);
                $real_key='real_t'.($i+1);
                $pwq_key='pw_'.($i+1);

                if(!array_key_exists($kpia_key,$d)){
                    $rt=is_null($groupdata)?$this->getKPIA($d,$i):$this->getKPIAForGrouping($d,$i);
                }
                else
                    $rt=$d[$kpia_key];

                $rt=round($rt,2);
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
                $d[$aw_key]=round($calculate,2);
            }
            $result[]=$d;

        }

        $accumulated=$this->accumulateTotalAchievement($result,!is_null($groupdata));
        $result=$this->filterData($result,'kpiresult',!is_null($groupdata));
        return [
            'data'=>$result,
            'totalAchievement'=>$accumulated['totalAchievement'],
            'indexAchievement'=>$accumulated['indexAchivement']
        ];
    }

    protected function fetchAccumulatedKPIProcess($groupdata=null){

        $kpiprocesses=$this->fetchKPIProcess($groupdata);
        $result=[];

        foreach($kpiprocesses as $curr){

            if( !is_null(@$curr['real_1'])&&!is_null(@$curr['pt_1']))
                $kt_1=intval(@$curr['real_1'])-intval(@$curr['pt_1']);
            else
                $kt_1=-1;

            if(!is_null($curr['real_2'])&&!is_null($curr['pt_2']))
                $kt_2=intval($curr['real_2'])-intval($curr['pt_2']);
            else
                $kt_2=-1;

            $curr['kpia_1']=is_null($groupdata)?$this->getKPIProcessIndex($kt_1):$this->getKPIAByIndex(@$curr['real_1'],$curr['unit']);
            $curr['kpia_2']=is_null($groupdata)?$this->getKPIProcessIndex($kt_2):$this->getKPIAByIndex($curr['real_2'],$curr['unit']);
            $curr['bColor_kpia_1']=@$this->getKPIProcessColor($kt_1);
            $curr['bColor_kpia_2']=$this->getKPIProcessColor($kt_2);

            $curr['aw_1']=round((@$curr['kpia_1']/100)*intval(@$curr['pw_1']),2);
            $curr['aw_2']=round(($curr['kpia_2']/100)*intval($curr['pw_2']),2);

            $result[]=$curr;
        }

        $accumulated=$this->accumulateTotalAchievement($result,!is_null($groupdata));
        $result=$this->filterData($result,'kpiprocess',!is_null($groupdata));

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
            case '%':
            case 'MV':
                sanitize_to_number($kpiresult,array_keys(KPIResultHeader::KPIRESULTDKEY));
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

    protected function applyUpdateKPIProcessFromArray($kpiprocess,$kpiprocessdeletelist){
        $curr_process_id=$kpiprocess['id'];
        $curr_process=$this->kpiprocesses()->find($curr_process_id);
        $kpiprocess=filter_is_number($kpiprocess,KPIProcess::FRONT_END_PROPERTY);


        if(!in_array($curr_process_id,$kpiprocessdeletelist) &&
         (array_key_exists('kpi_header_id',$kpiprocess) && $kpiprocess['kpi_header_id'] ) ){
            if(!is_null($curr_process)){
                $curr_process_prev=$curr_process->getPrev();
                !is_null($curr_process_prev)?$curr_process_prev->mapFromArr(KPIProcess::KPIPROCESSPREVKEY,$kpiprocess):null;
                $curr_process->unit=array_key_exists('unit',$kpiprocess)?$kpiprocess['unit']:$curr_process->unit;
                $curr_process->mapFromArr(KPIProcess::KPIPROCESSCURRKEY,$kpiprocess);
                $curr_process->save();
            }
            else{
                $this->applyCreatedKPIProcessFromArray($kpiprocess);
            }
        }
    }


    /**
     *
     * @return void
     */
    protected function applyCreatedKPIProcessFromArray($kpiprocess){
        $header_prev=$this->getPrev();
        $kpi_process_id=$kpiprocess['id'];
        $datamap=KPIProcess::getArrayMap(KPIProcess::KPIPROCESSCURRKEY,$kpiprocess);
        $curr_process=$this->kpiprocesses()->find($kpi_process_id);
        if(!$curr_process){
            $this->kpiprocesses()->attach([
                $kpiprocess['id'] => $datamap
            ]);
            if($header_prev){
                $datamap2=KPIProcess::getArrayMap(KPIProcess::KPIPROCESSPREVKEY,$kpiprocess);
                try{
                    $header_prev->kpiprocesses()->attach([
                        $kpiprocess['id'] => $datamap2
                    ]);
                }catch(Exception $err){

                }
            }
        }
        else{
            $curr_process->mapFromArr(KPIProcess::KPIPROCESSCURRKEY,$kpiprocess);
            $curr_process->save();
            $curr_process_prev=$curr_process->getPrev();
            !is_null($curr_process_prev)?$curr_process_prev->mapFromArr(KPIProcess::KPIPROCESSPREVKEY,$kpiprocess):null;
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

        $final_achievements['t1_n']=round($t1_fr * $this->weight_result + $t1_fp * $this->weight_process,2);
        $final_achievements['t2_n']=round($t2_fr * $this->weight_result + $t2_fp * $this->weight_process,2);

        $final_achievements['t1_i']=$this->getIndexAchievement($final_achievements['t1_n']);
        $final_achievements['t2_i']=$this->getIndexAchievement($final_achievements['t2_n']);

        $final_achievements['t1_f']=round($final_achievements['t1_n']-100,2);
        $final_achievements['t2_f']=round($final_achievements['t2_n']-100,2);

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

    public function fetchAccumulatedData($type,$groupdata=null){
        if($type==='kpiresult'){
            return $this->fetchAccumulatedKPIResult($groupdata);
        }
        else if($type==='kpiprocess'){
            return $this->fetchAccumulatedKPIProcess($groupdata);
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
        $updateMap=array_key_exists('updated',$kpiprocesses)?$kpiprocesses['updated']:[];
        $createMap=array_key_exists('created',$kpiprocesses)?$kpiprocesses['created']:[];

        foreach($updateMap as $kpiprocess){
            $this->applyUpdateKPIProcessFromArray(
                $kpiprocess,
                $kpiprocessdeletelist
            );
        }
        foreach($createMap as $kpiprocess){
            $this->applyCreatedKPIProcessFromArray($kpiprocess);
        }

        $this->applyDeletedKPIProcessFromArray($kpiprocessdeletelist);

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
     * Menghapus data kpiprocess yang berelasi dengan header ini
     *
     * @param array $kpiprocessdeletelist daftar ID dari kpiprocess yang mau dihapus
     * @return void
     */
    public function applyDeletedKPIProcessFromArray($kpiprocessdeletelist){
        $header_prev=$this->getPrev();
        $kpiprevdeletelist=[];
        $now=Carbon::now();
        if(count($kpiprocessdeletelist)!==0){
            $this->kpiprocesses()->detach($kpiprocessdeletelist);
            if($header_prev){
                foreach($kpiprocessdeletelist as $to_delete){
                    $d=$header_prev->kpiprocesses()->find($to_delete);

                    if(!$d->pivot)
                        continue;

                    $curr_date=Carbon::parse($d->pivot->created_at);
                    if($now->month === $curr_date->month && $now->year ===$curr_date->year)
                        $kpiprevdeletelist[]=$to_delete;
                }
                $header_prev->kpiprocesses()->detach($kpiprevdeletelist);
            }
        }
    }

    /**
     *
     *
     * @return int Nilai balik 1 jika berhasil, 0 jika gagal
     */
    public function makeKPIResult(KPIHeader $curr_header=null,$header_id=null){

        $header_id=is_null($header_id)?$this->id:$header_id;

        try{

            if(!is_null($curr_header) && $this->kpiresultheaders->count()===0 ){
                foreach($curr_header->kpiresultheaders as $resultheader){
                    KPIResultHeader::create([
                        'id'=>KPIResultHeader::generateID($this->employee->id,$header_id),
                        'kpi_result_id'=>$resultheader->kpi_result_id,
                        'kpi_header_id'=>$header_id,
                        'pw'=>$resultheader->pw,
                        'pt_t'=>$resultheader->pt_t,
                        'pt_k'=>$resultheader->pt_k,
                        'real_t'=>$resultheader->real_t,
                        'real_k'=>$resultheader->real_k
                    ]);
                }
            }
        }catch(\Exception $err){
            put_error_log($err);
            return 0;
        }
        return 1;
    }

    /**
     * Membuat KPIProcess bagi header yang baru
     *
     * @param App\Model\KPIHeader $curr_header Header yang mau di-mapping
     * @param string|null $header_id ID Dari header yang ingin dibuat
     *
     * @return int 1 jika berhasil, 0 jika ada error
     */
    public function makeKPIProcess(KPIHeader $curr_header=null,$header_id=null){

        if(!is_null($curr_header)){
            $h=$this;
            try{
                foreach($curr_header->kpiprocesses as $kpiprocess){
                    $h->kpiprocesses()->attach([
                        $kpiprocess->id=>[
                            'pw'=>$kpiprocess->pivot->pw,
                            'pt'=>$kpiprocess->pivot->pt,
                            'real'=>$kpiprocess->pivot->real
                        ]
                    ]);
                }
            }catch(\Exception $err){
                put_error_log($err);
                return 0;
            }
        }
        return 1;

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
        foreach($this->employee->getHirarcialEmployee() as $employee){
            if(!$this->hasEndorse($employee))
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

    /**
     * Menentukan apakah seorang karyawan sudah melakukan pengesahan pada PMS
     *
     * @param App\Model\Employee $employee Karyawan yang dituju
     * @return boolean
     */
    public function hasEndorse(Employee $employee){
        $r=$this->kpiendorsements()->where('employee_id',$employee->id)->first();
        return !is_null($r);
    }
}
