<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use App\Model\Employee;

class EndorsementNotification extends Notification
{
    use Queueable;

    protected $header;
    protected $employee;

    protected function getRedirectLink(){
        $header_id=$this->header->employee->id;
        $link="realisasi/$header_id";

        return $link;
    }

    protected function getMessage(){
        $message="%s sudah mengesahkan %s untuk periode %s";
        $header=$this->header;

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
        return $result;

    }

    public function __construct($header,$employee)
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
