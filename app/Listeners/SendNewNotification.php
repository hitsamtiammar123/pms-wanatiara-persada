<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use App\Events\NewNotification;
use App\Model\Employee;
use Illuminate\Broadcasting\BroadcastException;

class SendNewNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        $user=$event->notifiable;
        $notification=$user->getLatestNotification();

        try{
            is_null($notification)?:event(new NewNotification($user,Employee::frontEndNotification($notification)));
        }catch(BroadcastException $err){
            put_log('Notifikasi baru sudah muncul untuk '.$user->employee->name);
        }
    }
}
