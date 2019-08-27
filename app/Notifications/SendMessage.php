<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendMessage extends Notification
{
    use Queueable;

    protected $from,$message;

    public function __construct($from,$message)
    {
        $this->from=$from;
        $this->message=$message;
    }



    public function via($notifiable)
    {
        return ['database'];
    }


    public function toMail($notifiable)
    {

    }


    public function toArray($notifiable)
    {
        return [
            'type'=>'message',
            'subject'=>$this->message,
            'from'=>$this->from->employee->name
        ];
    }
}
