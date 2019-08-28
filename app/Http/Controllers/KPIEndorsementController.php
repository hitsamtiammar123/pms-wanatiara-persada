<?php

namespace App\Http\Controllers;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\KPIEndorsement;
use App\Notifications\EndorsementNotification;
use App\Http\Controllers\Traits\ErrorMessages;
use App\Notifications\SendMessage;
use App\Http\Controllers\Traits\BroadcastPMSChange;

class KPIEndorsementController extends Controller
{

    use ErrorMessages,BroadcastPMSChange;


    protected function fireEndorsementEvent($endorse){
        $auth_user=auth_user();
        $header=$endorse->kpiheader;
        $employee=$auth_user->employee;
        $userToSend=$endorse->employee->atasan->user;
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
                $endorse->verified=false;
                $endorse->save();
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
        //
        $endorse=KPIEndorsement::find($id);
        if($endorse){
            $endorse->verified=$request->verified;
            $endorse->save();
            $this->fireEndorsementEvent($endorse);

            return [
                'status'=>1,
                'message'=>'Sudah disahkan',
                'user'=>$endorse->kpiheader->employee
            ];
        }
        return send_404_error('Data tidak ditemukan');
    }

}
