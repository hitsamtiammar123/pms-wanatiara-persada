<?php

namespace App\Http\Controllers\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;

trait HeaderFetch{

    protected function fetchInputHeader(Request $request,$employee){
        $now=Carbon::now();

        $month=$request->has('month')?$request->month:$now->month;
        $year=$request->has('year')?$request->year:$now->year;

        return $employee->getHeader($month,$year);
    }

}
