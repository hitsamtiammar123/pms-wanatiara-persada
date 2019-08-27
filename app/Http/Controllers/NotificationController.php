<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\User;

class NotificationController extends Controller
{
    //

    protected function sendNotFound($id){
        return send_404_error('Notifikasi dengan id '.$id.' tidak ditemukan');
    }

    protected function sendUserNotFound($employeeID){
        return send_404_error('Pengguna dengan id '.$employeeID.' tidak ditemukan');
    }

    protected function sendMarkAsReadArray($n){
        return [
            'message'=>'Notifikasi sudah dilabel sebagai sudah dibaca',
            'data'=>$n
        ];
    }

    protected function sendHasReadArray($n){
        return [
            'message'=>'Notifikasi sudah dibaca sebelumnya',
            'data'=>$n
        ];
    }

    public function getNotification($employeeID,$id){
        $employee=Employee::find($employeeID);

        if($employee && $employee->isUser()){
            $notification=$employee->user->notifications->where('id',$id)->first();

            if($notification)
                return Employee::frontEndNotification($notification);
            else
                return $this->sendNotFound($id);

        }

        return $this->sendUserNotFound($employeeID);
    }

    public function getNotifications(Request $request,$employeeID){
        $employee=Employee::find($employeeID);

        if($employee && $employee->isUser() ){
            $page=$request->input('page');
            $page=$page?intval($page):1;

            $skip=($page-1)*5;

            $user=$employee->user;
            $notifications=$user->notifications->sortByDesc('created_at')->splice($skip)->take(5);

            return[
                'total'=>$user->notifications->count(),
                'unread'=>$user->unreadNotifications->count(),
                'page'=>$page,
                'data'=>Employee::frontEndNotifications($notifications)
            ];

        }

        return $this->sendUserNotFound($employeeID);
    }

    public function markAsRead(Request $request,$employeeID,$id){
        $employee=Employee::find($employeeID);
        if($employee && $employee->isUser()){
            $notifications=$employee->user->notifications;
            $n=$notifications->where('id',$id)->first();

            if(!$n)
                return $this->sendNotFound($id);

            if(is_null($n->read_at)){
                $n->markAsRead();
                return $this->sendMarkAsReadArray($n);
            }
            else
                return $this->sendHasReadArray($n);

        }

        return $this->sendUserNotFound($employeeID);
    }

    public function getRequestableUsers($employeeID){
        $employee=Employee::find($employeeID);

        if($employee){
            $result=[];
            $bawahan=$employee->bawahan;

            foreach($bawahan as $curr){
                $header=$curr->getCurrentHeader();
                $self_endorse=$header->getSelfEndorse();

                if($self_endorse->verified===1){
                    $r=[];
                    $r['id']=$curr->id;
                    $r['name']=$curr->name;
                    $r['sendTo']=$curr->getSendToUser();
                    $result[]=$r;
                }
            }

            return $result;
        }

        return $this->sendUserNotFound($employeeID);
    }
}
