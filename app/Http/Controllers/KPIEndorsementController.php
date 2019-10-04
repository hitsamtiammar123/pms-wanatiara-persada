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
use App\Model\KPITag;
use Illuminate\Support\Carbon;

class KPIEndorsementController extends Controller
{

    use ErrorMessages,BroadcastPMSChange;


    protected function fireEndorsementEvent($header,$h_employee){
        $auth_user=auth_user();

        $employee=$auth_user->employee;
        $userToSend=$employee->atasan->user;
        $userToSend->notify(new EndorsementNotification($header,$employee));

        $this->broadcastChange($h_employee);

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

    protected function doResetEndorsement($employee){
        $header=$employee->getCurrentHeader();

        $kpiendorsements=$header->kpiendorsements;
        foreach($kpiendorsements as $endorse){
            $endorse->delete();
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
            $this->doResetEndorsement($employee);

            if($employee->representativeTags->count()!==0)
                foreach($employee->representativeTags as $tag)
                    foreach($tag->groupemployee as $rep_employee)
                        $this->doResetEndorsement($rep_employee);
                    
            $this->approvedEndorseChange($notificationID);
            $this->sendApprovalRequest($employee,$employee->getCurrentHeader());

            return [
                'status'=>'Status Pengesahan sudah diubah'
            ];

        }
        else
            return $this->sendUserNotFound($employeeID);

    }


    public function updateGroup(Request $request,$id){
        $kpitag=KPITag::find($id);
        if($kpitag){
            $now=Carbon::now();
            $month=$request->input('month',$now->month);
            $year=$request->input('year',$now->year);
            $auth_user=auth_user();
            $auth_user_employee=$auth_user->employee;

            foreach($kpitag->groupemployee as $employee){
                $endorse_as=null;
                if($employee->getEndorsementLevel($auth_user_employee)===1)
                    $endorse_as=$employee;
                else
                    $endorse_as=$auth_user->employee;

                if(!$employee->isUser()){
                    $header=$employee->getHeader($month,$year);
                    $this->makeEndorsement($header,$endorse_as);
                }
                 
            }
            $this->fireEndorsementEvent($kpitag,$kpitag->representative);
            return [
                'status'=>1,
                'message'=>'PMS Group Sudah disahkan',
                'user'=>$kpitag->representative
            ];

        }
        return send_404_error('Data Tag tidak ditemukan');
    }

    public function update($id)
    {

        $header=KPIHeader::find($id);
        if($header){
            $auth_user=auth_user();
            $employee=$auth_user->employee;
            $this->makeEndorsement($header,$employee);
            $this->fireEndorsementEvent($header,$header->employee);
            return [
                'status'=>1,
                'message'=>'Sudah disahkan',
                'user'=>$header->employee
            ];

        }

        return send_404_error('Data tidak ditemukan');
    }

}
