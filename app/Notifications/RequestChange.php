<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestChange extends Notification
{
    use Queueable;

    protected $forUser;
    protected $message;
    protected $subject;
    protected $fromUser;

    public function __construct($forUser,$fromUser,$message,$subject)
    {
        $this->forUser=$forUser;
        $this->message=$message;
        $this->subject=$subject;
        $this->fromUser=$fromUser;
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
            'type'=>'request-change',
            'subject'=>$this->subject,
            'message'=>$this->message,
            'from'=>$this->fromUser->employee->name,
            'to'=>$this->forUser->toArray(),
            'approved'=>false
        ];
    }
}
