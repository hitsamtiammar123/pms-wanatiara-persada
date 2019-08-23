<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Role;
use App\Model\KPIHeader;
use App\Model\KPIResult;
use App\Model\User;

class EmployeeController extends Controller
{



    private function fetchIkhtisar($item){
        $item->load('role');
        $item->load('kpiheaders');
        $item->makeHidden(Employee::HIDDEN_PROPERTY);
        if($item->role!==null)
            $item->role->makeHidden(Role::HIDDEN_PROPERTY);
        $item->kpiheaders->each(function($d){
            $d->kpiresultheaders;
            $d->makeHidden(KPIHeader::HIDDEN_PROPERTY);
        });
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $employee=Employee::find($id);

        if(!$employee){
            $m='Data karyawan dengan ID '.$id.' tidak ditemukan';
            return send_404_error($m);
        }

        if($employee->atasan!==null){
            $employee->load('atasan.role');
            $employee->atasan=$employee->atasan->makeHidden(Employee::HIDDEN_PROPERTY);
            $employee->atasan->role=$employee->atasan->role->makeHidden(Role::HIDDEN_PROPERTY);
        }

        if($employee->bawahan!==null){
            $employee->load('bawahan.role');
            $employee->bawahan=$employee->bawahan->makeHidden(Employee::HIDDEN_PROPERTY);
            $employee->bawahan->each(function($data,$key){
                $data->role->makeHidden(Role::HIDDEN_PROPERTY);
            });
        }



        $employee->load('role');
        $employee=$employee->makeHidden(Employee::HIDDEN_PROPERTY);
        $employee->role=$employee->role->makeHidden(Role::HIDDEN_PROPERTY);
        //$employee->bawahan->role=$employee->bawahan->role->makeHidden(Role::HIDDEN_PROPERTY);

        return $employee;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function ikhtisar(Request $request){
        $employee_id=$request->input('employee');

        if(!$employee_id){
            $employees=Employee::where('role_id','!=','1915283263')->paginate(10);
            $items=$employees->items();
            foreach($items as $item){
                $this->fetchIkhtisar($item);
            }
            return $employees;
        }
        else{
            $employee=Employee::find($employee_id);
            $this->fetchIkhtisar($employee);

            return ['data'=>[$employee]];
        }

    }

    public function getNotification(Request $request,$employeeID){
        $employee=Employee::find($employeeID);

        if($employee && $employee->isUser() ){
            $page=$request->input('page');
            $page=$page?intval($page):1;

            $skip=($page-1)*5;

            $user=$employee->user;
            $notifications=$user->notifications->sortBy('created_at')->splice($skip)->take(5);

            return[
                'unread'=>$user->unreadNotifications->count(),
                'data'=>$notifications->makeHidden(User::HIDDEN_PROPERTY_NOTIFICATION)
            ];


        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
