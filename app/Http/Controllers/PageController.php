<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{

    public function index(){
        return view('index');
    }


    //
    public function app(Request $request){
        return view('app');
    }

    public function appfront(){
        return view('frontview');
    }

    public function printPms(Request $request,$employeeID){
        $employee=Employee::find($employeeID);

        $kpiheader=$this->fetchInputHeader($request,$employee);
        $kpiresults=$kpiheader->fetchAccumulatedData('kpiresult');
        $kpiprocesses=$kpiheader->fetchAccumulatedData('kpiprocess');
        $data=[
            'kpiresults'=>$kpiresults,
            'kpiprocesses'=>$kpiprocesses,
            'employee'=>$employee,
            'header'=>$kpiheader
        ];
        return view('print.pms',$data);
    }
}
