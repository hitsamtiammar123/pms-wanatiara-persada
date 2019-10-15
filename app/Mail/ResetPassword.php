<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $token;
    protected $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token,$email)
    {
        $this->token=$token;
        $this->email=$email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@test.com')->view('mail.reset-password',[
            'email'=>$this->email,
            'token'=>$this->token
        ]);
    }
}
