<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIHeader;
use App\Model\Employee;
use App\Model\KPIResult;
use App\Model\KPIEndorsement;
use App\Model\Role;
use App\Model\KPIProcess;
use App\Model\KPIResultHeader;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Traits\BroadcastPMSChange;

class KPIHeaderController extends Controller
{

    use BroadcastPMSChange;

    public function index()
    {
        //
    }


    public function show(Request $request,$id)
    {
        //

        $month=$request->input('month');
        if($month){
            $curr_date=KPIHeader::getDate($month);
        }
        else{
            $curr_date=KPIHeader::getCurrentDate();
        }
        $nc=new Carbon($curr_date);
        $next_date=$nc->addMonth();

        $kpiheaders=KPIHeader::where('employee_id',$id)->get();
        $kpiheader=$kpiheaders->where('period',$curr_date)->first();
        if(!$kpiheader){
            return send_404_error('Data tidak ditemukan');
        }

        $kpiheader->load('employee');
        $kpiheader->employee=$kpiheader->employee->makeHidden(Employee::HIDDEN_PROPERTY);
        $kpiheader->makeHidden(KPIHeader::HIDDEN_PROPERTY);

        $kpiheader->kpiendorsements->each(function($data,$key){
            $data->load('employee');
            $data->makeHidden(KPIEndorsement::HIDDEN_PROPERTY);
            $data->employee->makeHidden(Employee::HIDDEN_PROPERTY);
            $data->employee->load('role');
            $data->employee->role->makeHidden(Role::HIDDEN_PROPERTY);
        });

        $kpiheader->kpiprocesses->each(function($d){
            $d->pivot->makeHidden(KPIProcess::HIDDEN_PIVOT_PROPERTY);
        });

        $kpiheader_arr=$kpiheader->toArray();
        $kpiheader_arr['kpiresults']=$kpiheader->fetchFrontEndData('kpiresult');
        $kpiheader_arr['kpiendorsements']=$kpiheader->kpiendorsements->keyBy('level');
        $kpiheader_arr['kpiprocesses']=$kpiheader->fetchFrontEndData('kpiprocess');
        $kpiheader_arr['period_end']=$next_date->format('Y-m-d');
        $kpiheader_arr['period_start']=$kpiheader_arr['period'];


        unset($kpiheader_arr['period']);

        return $kpiheader_arr;
    }


    public function update(Request $request, $id)
    {
        //

        $header=KPIHeader::find($id);
        if(!$header){
            return send_404_error('Data Tidak ditemukan');
        }

        $header_next=$header->getNext();
        if(!$header_next){
            return send_404_error('Data pada bulan berikutnya Tidak ditemukan');
        }

        $res_data=$request->all();

        $kpiresults=json_decode($res_data['kpiresult'],true);
        $kpiprocesses=json_decode($res_data['kpiprocesses'],true);
        $kpiresultdeletelist=json_decode($res_data['kpiresultdeletelist'],true);
        $kpiprocessdeletelist=json_decode($res_data['kpiprocessdeletelist'],true);
        $errors=[];

        foreach($kpiresultdeletelist as $todelete){
            $curr_delete=KPIResultHeader::find($todelete);
            if($curr_delete){
                $curr_delete_next=$curr_delete->getNext();

                    $curr_delete->delete();
                    $curr_delete_next?$curr_delete_next->delete():null;

            }
        }

        foreach($kpiresults as $kpiresult){
            $kpiresult=filter_is_number($kpiresult,KPIResultHeader::FRONT_END_PROPERTY);
            if(!is_null($kpiresult['id'])){
                $curr_result=KPIResultHeader::find($kpiresult['id']);

                $curr_result_next=$curr_result->getNext();
                $curr_result->pw=$kpiresult['pw_1'];
                $curr_result->pt_t=$kpiresult['pt_t1'];
                $curr_result->pt_k=$kpiresult['pt_k1'];
                $curr_result->real_t=$kpiresult['real_t1'];
                $curr_result->real_k=$kpiresult['real_k1'];

                $curr_result_next->pw=$kpiresult['pw_2'];
                $curr_result_next->pt_t=$kpiresult['pt_t2'];
                $curr_result_next->pt_k=$kpiresult['pt_k2'];
                $curr_result_next->real_t=$kpiresult['real_t2'];
                $curr_result_next->real_k=$kpiresult['real_k2'];

                $curr_result->kpiresult->name=$kpiresult['name'];
                $curr_result->kpiresult->unit=$kpiresult['unit'];


                    $curr_result->push();
                    $curr_result_next->save();

            }
            else{
                $curr_result=new KPIResultHeader();
                $curr_result->id=KPIResultHeader::generateID($header->employee->id,$header->id);
                $curr_result->kpi_header_id=$header->id;

                $curr_result_next=new KPIResultHeader();
                $curr_result_next->id=KPIResultHeader::generateID($header_next->employee->id,$header_next->id);
                $curr_result_next->kpi_header_id=$header_next->id;

                $new_result=new KPIResult();
                $new_result_id=KPIResult::generateID($header->employee->id);
                $new_result->id=$new_result_id;
                $new_result->name=$kpiresult['name'];
                $new_result->unit=$kpiresult['unit'];

                $curr_result->kpi_result_id=$new_result_id;
                $curr_result_next->kpi_result_id=$new_result_id;


                $curr_result->pw=$kpiresult['pw_1'];
                $curr_result->pt_t=$kpiresult['pt_t1'];
                $curr_result->pt_k=$kpiresult['pt_k1'];
                $curr_result->real_t=$kpiresult['real_t1'];
                $curr_result->real_k=$kpiresult['real_k1'];

                $curr_result_next->pw=$kpiresult['pw_2'];
                $curr_result_next->pt_t=$kpiresult['pt_t2'];
                $curr_result_next->pt_k=$kpiresult['pt_k2'];
                $curr_result_next->real_t=$kpiresult['real_t2'];
                $curr_result_next->real_k=$kpiresult['real_k2'];

                    $new_result->save();
                    $curr_result->save();
                    $curr_result_next->save();
                    //$curr_result->kpiresult->save();


            }

        }

        $kpiprocess_save=[];
        $kpiprocess_save_n=[];

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

            $header->kpiprocesses()->sync($kpiprocess_save);
            $header_next->kpiprocesses()->sync($kpiprocess_save_n);

        $employee=$header->employee;
        $this->broadcastChange($employee);


        return [
            'message'=>'Berhasil'
        ];
    }
}
