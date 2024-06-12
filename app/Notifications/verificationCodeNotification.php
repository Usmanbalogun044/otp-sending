<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\NexmoMessage;

class VerificationCodeNotification extends Notification
{
    use Queueable;

    protected $verificationCode;

    /**
     * Create a new notification instance.
     *
     * @param string $verificationCode
     * @return void
     */
    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'nexmo']; // Send via both email and SMS (Nexmo)
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Your verification code is: ' . $this->verificationCode)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return Nexmo
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
            ->content('Your verification code is: ' . $this->verificationCode);
    }
}
