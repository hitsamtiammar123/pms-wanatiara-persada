<?php

namespace App\Http\Controllers\Traits;

use App\Events\PMSHasChanged;
use Illuminate\Broadcasting\BroadcastException;

trait BroadcastPMSChange{

    public function broadcastChange($employee){
        if($employee->isUser()){
            $auth_user=auth_user();
            try{
                if($auth_user->employee_id===$employee->atasan->id)
                    event(new PMSHasChanged($employee->user,$employee));
                else if($auth_user->employee_id===$employee->id)
                    event(new PMSHasChanged($employee->atasan->user,$employee));
                }
            catch(BroadcastException $err){
                $message="PMS dari {$employee->name} sudah diubah oleh {$auth_user->employee->name}";
                put_log($message);
            }
        }
    }

}
