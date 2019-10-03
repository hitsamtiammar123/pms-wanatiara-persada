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
            $employee_id=$employee->id;

            KPIEndorsement::create([
                'id' =>KPIEndorsement::generateID($employee_id),
                'kpi_header_id' =>$id,
                'employee_id'=>$employee_id,
                'level'=>$employee->getEndorsementLevel($header->employee)
            ]);

            return [
                'status'=>1,
                'message'=>'Sudah disahkan',
                'user'=>$header->employee
            ];

        }

        return send_404_error('Data tidak ditemukan');
    }

}
