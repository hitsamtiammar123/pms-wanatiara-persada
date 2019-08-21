<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\KPIHeader;
use PDF;

class PDFController extends Controller
{
    //
    public function bacoba(){
        $pdf=PDF::loadView('pdf.pdf-hehe');
        return $pdf->setPaper('a4','portrait')->stream('ini-pdf.pdf');
    }

    public function pms($kpiheaderid){
        $kpiheader=KPIHeader::find($kpiheaderid);
        $kpiresults=$kpiheader->fetchAccumulatedData('kpiresult');
        $kpiprocesses=$kpiheader->fetchAccumulatedData('kpiprocess');
        $data=[
            'kpiresult'=>$kpiresults,
            'kpiprocess'=>$kpiprocesses
        ];

        return $data;
    }
}
