<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use App\Model\Employee;
use App\Model\Interfaces\Endorseable;

class EndorsementNotification extends Notification
{
    use Queueable;

    protected $header;
    protected $employee;

    protected function getRedirectLink(){
        $link='';
        if($this->header instanceof \App\Model\KPIHeader){
            $employee_id=$this->header->employee->id;
            $cdate=$this->header->cPeriod();
            $link="realisasi/$employee_id/".($cdate->month-1).'/'.$cdate->year;
        }
        else if($this->header instanceof \App\Model\KPITag){
            $e_zero=$this->header->getZeroIndexEmployee();
            $cdate=$e_zero?$e_zero->getCurrentHeader()->cPeriod():Carbon::now();
            $link="realisasi-group/{$this->header->id}".($cdate->month-1).'/'.$cdate->year;
        }

        return $link;
    }

    protected function getMessage(){
        $result='';
        $header=$this->header;

        if($header instanceof \App\Model\KPIHeader){
            $message="%s sudah mengesahkan %s untuk periode %s";
            $employee_name=$this->employee->name;
            $headerTo='';

            if($header->employee->id === $this->employee->id){
                $headerTo='PMS nya sendiri';
            }
            else{
                $headerTo='PMS dari '.$header->employee->name;
            }


            $period=Carbon::parse($header->period)->format('M Y');
            $result=sprintf($message,$employee_name,$headerTo,$period);
        }
        else if($header instanceof \App\Model\KPITag){

            $message="%s sudah mengesahkan %s";
            $employee_name=$this->employee->name;
            $headerTo="PMS group dari \"{$header->name}\"";
            $result=sprintf($message,$employee_name,$headerTo);

        }

        return $result;

    }

    public function __construct(Endorseable $header,$employee)
    {
        $this->header=$header;
        $this->employee=$employee;
    }


    public function via($notifiable)
    {
        return ['database'];
    }


    public function toMail($notifiable)
    {

    }

    public function getArrayData(){
        return [
            'type'=>'redirect',
            'subject'=>$this->getMessage(),
            'redirectTo'=>$this->getRedirectLink(),
            'from'=>$this->employee->name
        ];
    }


    public function toArray($notifiable)
    {
        return $this->getArrayData();
    }
}
