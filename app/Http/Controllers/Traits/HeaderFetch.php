<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Model\KPITag;

trait HeaderFetch{

    protected function fetchInputHeader(Request $request,$employee){
        $now=Carbon::now();

        $month=$request->has('month')?$request->month:$now->month;
        $year=$request->has('year')?$request->year:$now->year;

        return $employee->getHeader($month,$year);
    }

    protected function fetchValidTagEmployee(Request $request, KPITag $tag){
        $now=Carbon::now();

        $month=$request->has('month')?$request->month:$now->month;
        $year=$request->has('year')?$request->year:$now->year;

        return $tag->getZeroIndexEmployee($month,$year);
    }

}
