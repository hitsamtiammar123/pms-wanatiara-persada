<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\Role;
use App\Model\KPIHeader;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Traits\ErrorMessages;
use Carbon\Carbon;

class EmployeeController extends Controller
{

    use ErrorMessages;

    private function fetchIkhtisar($item,$currYear){
        $item->load('role');
        $item->load('kpiheaders');
        $item->makeHidden(Employee::HIDDEN_PROPERTY);
        if($item->role!==null)
            $item->role->makeHidden(Role::HIDDEN_PROPERTY);

        $item->kpiheaders->each(function($d,$index)use($item,$currYear){
            $carbon=Carbon::parse($d->period);
            if($carbon->year==$currYear){
                $d->kpiresultheaders;
                $d->makeHidden(KPIHeader::HIDDEN_PROPERTY);
            }
            else
                $item->kpiheaders->forget($index);
        });
        $values=$item->kpiheaders->values()->all();
        unset($item->kpiheaders);
        $item->kpiheaders=$values;
        
    }



    public function store(Request $request)
    {
        //
    }


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




    public function update(Request $request, $id)
    {

        $employee=Employee::find($id);
        if(!$employee)
            return $this->sendUserNotFound($id);

        $validateInput=$request->validate([
            'email'=>['required','email',Rule::unique('users')->ignore($employee->user->id)],
            'name'=>'required',
            'gender'=>'required|in:male,female'
        ]);

        $employee->name=$validateInput['name'];
        $employee->user->email=$validateInput['email'];
        $employee->gender=$validateInput['gender'];
        $employee->push();

        return [
            'status'=>'berhasil'
        ];
    }

    public function updatePassword(Request $request, $id){
        $employee=Employee::find($id);
        if(!$employee)
            return $this->sendUserNotFound($id);

        $validateInput=$request->validate([
            'password'=>['required',function($attribute, $value, $fail)use($employee){
                if(!Hash::check($value, $employee->user->password,[]))
                    $fail('Password lama tidak sesuai');
            }],
            'new'=>'required',
            'retype'=>'required|same:new'
        ]);

        $employee->user->password=bcrypt($validateInput['new']);
        $employee->push();

        return [
            'status'=>'berhasil'
        ];

    }

    public function ikhtisar(Request $request){
        $employee_id=$request->input('employee');
        $year=$request->input('year',Carbon::now()->year);

        if(!$employee_id){
            $employees=Employee::where('role_id','!=','1915283263')->paginate(10);
            $items=$employees->items();
            foreach($items as $item){
                $this->fetchIkhtisar($item,$year);
            }
            return $employees;
        }
        else{
            $employee=Employee::find($employee_id);
            if(is_null($employee))
                return send_404_error();

            $this->fetchIkhtisar($employee,$year);

            return ['data'=>[$employee]];
        }

    }




    public function destroy($id)
    {
        //
    }
}
