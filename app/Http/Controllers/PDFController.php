<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class PDFController extends Controller
{
    //
    public function bacoba(){
        $pdf=PDF::loadView('pdf.pdf-hehe');
        return $pdf->setPaper('a4','portrait')->stream('ini-pdf.pdf');
    }
}
