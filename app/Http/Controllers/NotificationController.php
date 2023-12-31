<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\User;
use App\Notifications\RequestChange;
use App\Notifications\SendMessage;
use Carbon\Carbon;
use App\Http\Controllers\Traits\ErrorMessages;
use App\Model\PMSLog;
use Illuminate\Database\Eloquent\Builder;

class NotificationController extends Controller
{
    //

    use ErrorMessages;

    protected function getRequestMessage($fromUser){
        return sprintf("%s sudah mengirimkan permintaan perubahan pengesahan untuk periode %s",
        $fromUser->employee->name,
        Carbon::now()->format('M Y'));
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

    protected function fetchLogs($searchText){
        return PMSLog::whereHas('user.employee',function(Builder $query)use($searchText){
            is_null($searchText)?:$query->where('employees.name','like','%'.$searchText.'%');
        })->get();
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
                $hasEndorse=$header->hasEndorse($curr);

                if($hasEndorse && !$curr->hasRequestChange()){
                    $sendToUser=$curr->getSendToUser();
                    $r=[];
                    $r['id']=$curr->id;
                    $r['name']=$curr->name;
                    $r['sendTo']=$sendToUser;
                    $result[]=$r;
                }
            }

            return $result;
        }

        return $this->sendUserNotFound($employeeID);
    }

    public function requestChange(Request $request,$employeeID){

        $forUser=Employee::find($employeeID);

        if($forUser){
            $to=$request->to;
            $toUser=Employee::find($request->to);
            if($toUser){
                $fromUser=auth_user();
                if($fromUser){
                    $toUser->user->notify(new RequestChange($forUser,$fromUser,$request->message,$request->subject));
                    $forUser->user->notify(new SendMessage($fromUser,$this->getRequestMessage($fromUser)));

                    $fromUser->makeLog($request,'endorse',"{$fromUser->employee->name} telah meminta perubahan status pengesahan untuk {$forUser->name}");
                    return [
                        'status'=>'Permintaan Perubahan status sudah dikirim'
                    ];
                }
                else
                    $this->sendAuthUserNotFound();

            }
            else
                $this->sendUserNotFound($to);

        }
        else
            $this->sendUserNotFound($employeeID);


    }

    public function getLogs(Request $request){
        $page=$request->input('page');
        $searchText=$request->input('q');
        $page=$page?intval($page):1;

        $skip=($page-1)*5;

        $logs=$this->fetchLogs($searchText)->sortByDesc('created_at')->splice($skip)
        ->take(5);
        $logs->each(function($data){
            $cCarbon=Carbon::parse($data->created_at);
            $data->created_at=$cCarbon->format('d F Y H:i:s');
            $data->user->makeHidden(['email','email_verified_at']);
        });

        return [
            'page'=>$page,
            'count'=>count($logs),
            'data'=>$logs
        ];
    }
}
