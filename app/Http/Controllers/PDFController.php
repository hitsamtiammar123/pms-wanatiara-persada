<?php

namespace App\Http\Controllers;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\KPIHeader;
use Carbon\Carbon;
use PDF;

class PDFController extends Controller
{
    protected function fetchInputHeader(Request $request,$employee){
        $now=Carbon::now();

        $month=$request->has('month')?$request->month:$now->month;
        $year=$request->has('year')?$request->year:$now->year;

        return $employee->getHeader($month,$year);
    }

    public function bacoba(){
        $pdf=PDF::loadView('pdf.pdf-hehe');
        return $pdf->setPaper('a4','portrait')->stream('ini-pdf.pdf');
    }


    public function pms(Request $request,$employeeID){
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

        $pdf=PDF::loadView('pdf.pdf-pms',$data);
        //return $pdf->setPaper('a4','landscape')->stream('test.pdf');
        return $pdf->stream('test.pdf');

    }
}
