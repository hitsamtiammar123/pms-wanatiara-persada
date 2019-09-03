<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HeaderFetch;
use App\Model\Employee;
use Illuminate\Http\Request;

class PageController extends Controller
{

    use HeaderFetch;

    public function index(){
        return view('index');
    }


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
            'header'=>$kpiheader,
            'title'=>'Performance Management System (PMS) - '.$employee->name.' - Periode: '.$kpiheader->cPeriod()->format('d F Y')
        ];
        return view('print.pms',$data);
    }
}
