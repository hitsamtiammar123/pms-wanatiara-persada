<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Role;
use App\Model\Employee;
use DB;

class SearchController extends Controller
{
    //

    protected $display_only=['id','name'];

    protected function fetchRoleResults($query,$role_level){
        $result=Role::select([DB::raw("name as item"),DB::raw("'role' as type")])
        ->where('name','like','%'.$query.'%')->where('level','>',$role_level)->get()->take(10);
        return $result;

    }

    protected function fetchEmployeeResults($query,$role_level){
        return Employee::select([DB::raw("employees.name as item"),DB::raw("'employee' as type")])
        ->join('roles','employees.role_id','=','roles.id')
        ->where('employees.name','like','%'.$query.'%')->where('roles.level','>',$role_level)->get()->take(10);
    }

    protected function getRoleData($search,$role_level,$get_fetch=false){
        $query=Role::where('level','>',$role_level);
        //$result=Role::where('name',$search)->where(DB::raw('level > '.$role_level))->first();
        $result=!$get_fetch?$query->where('name',$search)->first():$query->where('name','like','%'.$search.'%')->first();
        if($result){
            $result->load('employee');
            $result=$result->employee->count()!==0?$result->employee[0]:null;
        }
        return $result;
    }

    protected function getEmployeeData($search,$role_level){
        return Employee::select(DB::raw('employees.*'))->join('roles','employees.role_id','=','roles.id')->
        where('employees.name','=',$search)->where('roles.level','>',$role_level)->first();
    }

    public function autocomplete(Request $request){
        $query=$request->input('query');
        $auth_user=auth_user();

        $role_level=$auth_user->employee->role->level;

        if(!isset($query)||empty($query)){
            return send_406_error('Data query tidak boleh kosong');
        }

        $r1=$this->fetchRoleResults($query,$role_level);
        $r2=$this->fetchEmployeeResults($query,$role_level);

        $result=$r1->concat($r2);
        return $result;
    }

    public function result(Request $request){
        $search=$request->input('search');
        $type=$request->input('type');
        $isAuto=$request->input('isAuto');
        $auth_user=auth_user();

        if(!$auth_user)
            return send_401_error();

        $role_level=$auth_user->employee->role->level;

        if($type==='role')
            $result=$this->getRoleData($search,$role_level);

        else if($type==='employee'){
            $result=$this->getEmployeeData($search,$role_level);
            if(!$result && $isAuto==='true')
                $result=$this->getRoleData($search,$role_level,true);
        }

        return $result?$result->only($this->display_only):null;

    }
}
