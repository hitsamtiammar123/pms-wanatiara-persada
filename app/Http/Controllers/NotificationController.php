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
            $notifications=$user->notifications->sortBy('created_at')->splice($skip)->take(5);

            return[
                'total'=>$user->notifications->count(),
                'unread'=>$user->unreadNotifications->count(),
                'page'=>$page,
                'data'=>Employee::frontEndNotifications($notifications)
            ];


        }

        return null;

    }
}
