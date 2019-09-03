<?php

namespace App\Http\Controllers;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\KPIHeader;
use Carbon\Carbon;
use PDF;
use App\Http\Controllers\Traits\HeaderFetch;

class PDFController extends Controller
{

    use HeaderFetch;

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
