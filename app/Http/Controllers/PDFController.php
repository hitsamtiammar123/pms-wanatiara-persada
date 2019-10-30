<?php

namespace App\Http\Controllers;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\KPIHeader;
use Carbon\Carbon;
use PDF;
use App\Http\Controllers\Traits\HeaderFetch;
use App\Model\KPITag;

class PDFController extends Controller
{

    use HeaderFetch;

    public function bacoba(){
        $pdf=PDF::loadView('pdf.pdf-hehe');
        return $pdf->setPaper('a4','portrait')->stream('ini-pdf.pdf');
    }


    public function pms(Request $request,$employeeID){
        $employee=Employee::find($employeeID);
        if(!$employee)
            return send_404_error('Data Karyawan Tidak ditemukan');

        $kpiheader=$this->fetchInputHeader($request,$employee);
        if(!$kpiheader){
            return send_404_error('PMS tidak ditemukan');
        }
        $kpiresults=$kpiheader->fetchAccumulatedData('kpiresult');
        $kpiprocesses=$kpiheader->fetchAccumulatedData('kpiprocess');
        $data=[
            'kpiresults'=>$kpiresults,
            'kpiprocesses'=>$kpiprocesses,
            'employee'=>$employee,
            'header'=>$kpiheader,
            'title'=>"Performance Management System (PMS) - $employee->name - Periode: {$kpiheader->cPeriod()->format('F Y')}"
        ];

        $pdf=PDF::loadView('pdf.pdf-pms',$data);
        return $pdf->stream('test.pdf');

    }

    public function pmsGroup(Request $request,$tagID){
        $kpitag=KPITag::find($tagID);
        if(!$kpitag)
            return send_404_error('Data PMS Group Tidak ditemukan');

        $curr_header=$this->fetchInputHeader($request,$kpitag->getZeroIndexEmployee());
        if(!$curr_header)
            return send_404_error('Data KPI Header Tidak ditemukan');

        $data=[
            'kpitag'=>$kpitag,
            'curr_header'=>$curr_header
        ];

        $pdf=PDF::loadView('pdf.pdf-pms-group',$data);
        return $pdf->stream('test.pdf');
    }
}
