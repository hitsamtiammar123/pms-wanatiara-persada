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

class KPIHeaderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $kpiheader_arr['kpiendorsements']=$kpiheader->kpiendorsements;
        $kpiheader_arr['kpiprocesses']=$kpiheader->fetchFrontEndData('kpiprocess');
        $kpiheader_arr['period_end']=$next_date->format('Y-m-d');
        $kpiheader_arr['period_start']=$kpiheader_arr['period'];


        unset($kpiheader_arr['period']);

        return $kpiheader_arr;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $errors=[];

        foreach($kpiresults as $kpiresult){
            $curr_result=KPIResultHeader::find($kpiresult['id']);
            if($curr_result){
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

                try{
                    $curr_result->save();
                    $curr_result_next->save();
                    $curr_result->kpiresult->save();
                }catch(Exception $err){
                    $errors[]=$err->getMessage();
                }
            }

        }

        $kpiprocess_save=[];
        $kpiprocess_save_n=[];

        foreach($kpiprocesses as $kpiprocess){

            $curr_process_id=$kpiprocess['id'];
            $curr_process=KPIProcess::find($curr_process_id);
            $curr_process->unit=$kpiprocess['unit'];
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

            try{
                $curr_process->save();
            }catch(Exception $err){
                $errors[]=$err->getMessage();
            }
        }

        try{
            $header->kpiprocesses()->sync($kpiprocess_save);
            $header_next->kpiprocesses()->sync($kpiprocess_save_n);
        }catch(Exception $err){
            $errors[]=$err->getMessage();
        }

        return [
            'message'=>count($errors)===0?'Berhasil':'Ada error',
            'errors'=>$errors
        ];
    }
}
