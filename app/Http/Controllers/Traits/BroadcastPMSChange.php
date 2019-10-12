<?php

namespace App\Http\Controllers\Traits;

use App\Events\PMSHasChanged;
use App\Model\KPIHeader;
use App\Model\KPITag;
use Illuminate\Broadcasting\BroadcastException;

trait BroadcastPMSChange{

    protected function broadcastChange($request,$employee){
        if($employee->isUser()){
            $auth_user=auth_user();
            try{
                event(new PMSHasChanged($employee->user,$employee));
                event(new PMSHasChanged($employee->atasan->user,$employee));
            }
            catch(BroadcastException $err){
                $message="PMS dari {$employee->name} sudah diubah oleh {$auth_user->employee->name}";
                put_log($message);
            }
        }
    }

    protected function broadcastGroupChange($request,$kpitag,$hasChange=false){
        $auth_user=auth_user();
        if($auth_user){
            $name_tag=$kpitag->name;
            $message="{$auth_user->employee->name} telah melakukan perubahan terhadap group PMS \"{$name_tag}\"";
            !$hasChange?:$auth_user->makeLog($request,'update',$message);
        }
    }

    protected function broadcastLogPMS($request,$employee){
        if($employee->isUser()){
            $auth_user=auth_user();
            if($auth_user){
                try{
                    $for_pms='';
                    if($auth_user->employee_id===$employee->atasan->id)
                        $for_pms='PMS dari '.$employee->name;

                    else if($auth_user->employee_id===$employee->id)
                        $for_pms="PMS-nya sendiri ";

                    $auth_user->makeLog($request,'update',"{$auth_user->employee->name} Telah melakukan perubahan kepada {$for_pms}");
                }catch(BroadcastException $err){
                    put_error_log($err);
                }
            }
        }
    }

    protected function broadcastLogEndorsementPMS($request,$employee,$header){
        if($employee->isUser()){
            $auth_user=auth_user();
            if($auth_user){
                $period='';
                try{
                    $for_pms='';

                    if($header instanceof KPIHeader){
                        if($auth_user->employee_id===$employee->atasan->id){
                            $for_pms='PMS dari '.$employee->name;
                            $period=$header->cPeriod()->format('F Y');
                        }
                        else if($auth_user->employee_id===$employee->id){
                                $for_pms="PMS-nya sendiri ";
                                $period=$header->cPeriod()->format('F Y');
                        }
                    }
                    else if($header instanceof KPITag){
                        $for_pms="PMS Group \"{$header->name}\"";
                        $period_arr=$this->getPeriodFromRequest($request);
                        $month=$period_arr['month'];
                        $year=$period_arr['year'];
                        $cPeriod=$header->getZeroIndexEmployee()->getHeader($month,$year)->cPeriod();
                        $period=$cPeriod->format('F Y');
                    }

                    $auth_user->makeLog($request,'update',"{$auth_user->employee->name} Telah melakukan pengesahan untuk {$for_pms} untuk period {$period}");
                }catch(BroadcastException $err){
                    put_error_log($err);
                }
            }
        }
    }

}
