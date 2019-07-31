<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Role;

class EmployeeController extends Controller
{



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


        $employee->load('atasan.role');
        $employee->load('bawahan.role');
        $employee->load('role');


        $employee=$employee->makeHidden(Employee::HIDDEN_PROPERTY);
        $employee->atasan=$employee->atasan->makeHidden(Employee::HIDDEN_PROPERTY);
        $employee->bawahan=$employee->bawahan->makeHidden(Employee::HIDDEN_PROPERTY);
        $employee->role=$employee->role->makeHidden(Role::HIDDEN_PROPERTY);
        $employee->atasan->role=$employee->atasan->role->makeHidden(Role::HIDDEN_PROPERTY);
        $employee->bawahan->each(function($data,$key){
            $data->role->makeHidden(Role::HIDDEN_PROPERTY);
        });
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
