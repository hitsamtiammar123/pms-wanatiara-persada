<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\User;

class NotificationController extends Controller
{
    //
    public function getNotification(Request $request,$employeeID){
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

        return null;
    }

    public function markAsRead(Request $request,$employeeID,$id){
        $employee=Employee::find($employeeID);
        if($employee && $employee->isUser()){
            $notifications=$employee->user->notifications;
            $n=$notifications->where('id',$id)->first();

            if(!$n->read_at)
                $n->markAsRead();
            return [
                'message'=>'Notifikasi sudah dilabel sebagai sudah dibaca',
                'data'=>$n
            ];
        }

        return null;
    }
}
