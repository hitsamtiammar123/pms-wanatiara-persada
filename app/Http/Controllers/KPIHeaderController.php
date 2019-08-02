<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIHeader;
use App\Model\Employee;
use App\Model\KPIResult;
use App\Model\KPIEndorsement;
use App\Model\Role;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
        $curr_date=isset($month)?KPIHeader::getDate($month):KPIHeader::getCurrentDate();
        $kpiheader=KPIHeader::where('employee_id',$id)->where('period_start',$curr_date)->first();
        if(!$kpiheader){
            return response()->json(['status'=>'Data tidak ditemukan','error'=>1],404);
        }

        $kpiheader->load('employee');
        $kpiheader->employee=$kpiheader->employee->makeHidden(Employee::HIDDEN_PROPERTY);
        $kpiheader->makeHidden(KPIHeader::HIDDEN_PROPERTY);

        $kpiheader->kpiresults->each(function($data,$key){
            $data->makeHidden(KPIResult::HIDDEN_PROPERTY);
        });

        $kpiheader->kpiendorsements->each(function($data,$key){
            $data->load('employee');
            $data->makeHidden(KPIEndorsement::HIDDEN_PROPERTY);
            $data->employee->makeHidden(Employee::HIDDEN_PROPERTY);
            $data->employee->load('role');
            $data->employee->role->makeHidden(Role::HIDDEN_PROPERTY);
        });

        $kpiheader_arr=$kpiheader->toArray();
        $kpiheader_arr['kpiresults']=$kpiheader->kpiresults;
        $kpiheader_arr['kpiendorsements']=$kpiheader->kpiendorsements->sortBy('level');

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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
