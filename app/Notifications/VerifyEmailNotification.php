<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;
use App\Mail\VerifyEmail as VerifyEmailMail;

class VerifyEmailNotification extends VerifyEmail
{
    use Queueable, SerializesModels;

    public function toMail($notifiable)
    {


        $verificationUrl = $this->verificationUrl($notifiable);

        return (new VerifyEmailMail($notifiable, $verificationUrl))
            ->to($notifiable->email);
    }
}
