<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIHeader;
use App\Model\Employee;
use App\Model\KPIResult;
use App\Model\KPIEndorsement;
use App\Model\Role;
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

        // $kpiheader->kpiresults->each(function($data,$key){
        //     $data->makeHidden(KPIHeaderResult::HIDDEN_PROPERTY);
        // });

        $kpiheader->kpiendorsements->each(function($data,$key){
            $data->load('employee');
            $data->makeHidden(KPIEndorsement::HIDDEN_PROPERTY);
            $data->employee->makeHidden(Employee::HIDDEN_PROPERTY);
            $data->employee->load('role');
            $data->employee->role->makeHidden(Role::HIDDEN_PROPERTY);
        });

        $kpiheader_arr=$kpiheader->toArray();
        $kpiheader_arr['kpiresults']=$kpiheader->fetchFrontEndData();
        $kpiheader_arr['kpiendorsements']=$kpiheader->kpiendorsements;
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
        return $request->all();
    }


}
