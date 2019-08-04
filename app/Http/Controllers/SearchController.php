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
        if(!isset($query)||empty($query)){
            return send_406_error('Data query tidak boleh kosong');
        }

        $r1=Role::select([DB::raw("name as item"),DB::raw("'role' as type")])->where('name','like','%'.$query.'%')->get()->take(10);
        $r2=Employee::select([DB::raw("name as item"),DB::raw("'employee' as type")])->where('name','like','%'.$query.'%')->get()->take(10);

        $result=$r1->concat($r2);
        return $result;
    }

    public function result(Request $request){
        $search=$request->input('search');
        $type=$request->input('type');

        if($type==='role'){
            $result=Role::where('name',$search)->first();
            $result->load('employee');
            return $result->employee[0]->only(['id','name']);
        }
        else if($type==='employee'){
            $result=Employee::where('name',$search)->first();
            return $result->only(['id','name']);
        }
        else{

        }

    }
}
