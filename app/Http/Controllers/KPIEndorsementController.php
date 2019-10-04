<?php

namespace App\Http\Controllers;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\KPIEndorsement;
use App\Notifications\EndorsementNotification;
use App\Http\Controllers\Traits\ErrorMessages;
use App\Notifications\SendMessage;
use App\Http\Controllers\Traits\BroadcastPMSChange;
use App\Model\KPIHeader;

class KPIEndorsementController extends Controller
{

    use ErrorMessages,BroadcastPMSChange;


    protected function fireEndorsementEvent($header){
        $auth_user=auth_user();

        $employee=$auth_user->employee;
        $userToSend=$employee->atasan->user;
        $userToSend->notify(new EndorsementNotification($header,$employee));

        $employee=$header->employee;
        $this->broadcastChange($employee);

    }

    protected function approvedEndorseChange($notificationID){
        $auth_user=auth_user();

        $notifications=$auth_user->notifications;
        $n=$notifications->where('id',$notificationID)->first();

        $data=$n->data;

        $data['approved']=true;

        $n->data=$data;
        $n->save();

    }


    protected function sendApprovalRequest($employee,$header){
        if($employee->isUser()){
            $auth_user=auth_user();
            $user=$employee->user;
            $message= sprintf("Perubahan Status untuk periode %s sudah disetujui ",$header->period);
            $user->notify(new SendMessage($auth_user,$message));
        }
    }

    protected function makeEndorsement(KPIHeader $header,Employee $employee,$level=null){
        $employee_id=$employee->id;
        if(!$header->kpiendorsements()->where('employee_id',$employee_id)->first()){
            $level=is_null($level)?$employee->getEndorsementLevel($header->employee):$level;
            $header->kpiendorsements()->create([
                'id' =>KPIEndorsement::generateID($employee_id),
                'employee_id'=>$employee_id,
                'level'=>$level
            ]);
        }
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }

    public function reset(Request $request,$employeeID){
        $employee=Employee::find($employeeID);
        if($employee){
            $notificationID=$request->notificationID;
            $header=$employee->getCurrentHeader();

            $kpiendorsements=$header->kpiendorsements;
            foreach($kpiendorsements as $endorse){
                $endorse->delete();
            }

            $this->approvedEndorseChange($notificationID);
            $this->sendApprovalRequest($employee,$header);

            return [
                'status'=>'Status Pengesahan sudah diubah'
            ];

        }
        else
            return $this->sendUserNotFound($employeeID);

    }


    public function update(Request $request, $id)
    {

        $header=KPIHeader::find($id);
        if($header){
            $auth_user=auth_user();
            $employee=$auth_user->employee;
            $this->makeEndorsement($header,$employee);
            $this->fireEndorsementEvent($header);
            return [
                'status'=>1,
                'message'=>'Sudah disahkan',
                'user'=>$header->employee
            ];

        }

        return send_404_error('Data tidak ditemukan');
    }

}
