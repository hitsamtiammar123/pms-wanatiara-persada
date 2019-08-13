<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KPICompanyController extends Controller
{
    //

    public function getCurrentKPICompany(){
        return kpi_company();
    }
}
