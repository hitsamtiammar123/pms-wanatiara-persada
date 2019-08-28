<?php

namespace App\Http\Controllers\Traits;

use App\Events\PMSHasChanged;

trait BroadcastPMSChange{

    public function broadcastChange($employee){
        if($employee->isUser()){
            $auth_user=auth_user();
            if($auth_user->employee_id===$employee->atasan->id)
                event(new PMSHasChanged($employee->user,$employee));
            else if($auth_user->employee_id===$employee->id)
                event(new PMSHasChanged($employee->atasan->user,$employee));
        }
    }

}
