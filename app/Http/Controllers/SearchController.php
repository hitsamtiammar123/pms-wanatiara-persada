<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Role;
use App\Model\Employee;
use DB;

class SearchController extends Controller
{
    //

    public function autocomplete(Request $request){
        $query=$request->input('query');
        $r1=Role::select([DB::raw("name as item"),DB::raw("'role' as type")])->where('name','like','%'.$query.'%')->get()->take(10);
        $r2=Employee::select([DB::raw("name as item"),DB::raw("'employee' as type")])->where('name','like','%'.$query.'%')->get()->take(10);

        $result=$r1->concat($r2);
        return $result;
    }
}
