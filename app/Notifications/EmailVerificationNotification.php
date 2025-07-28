<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('HOSTYLE - 이메일 주소 인증')
            ->greeting('안녕하세요!')
            ->line('HOSTYLE에 가입해주셔서 감사합니다.')
            ->line('아래 버튼을 클릭하여 이메일 주소를 인증해주세요.')
            ->action('이메일 인증하기', $verificationUrl)
            ->line('이 요청을 하지 않으셨다면, 이 이메일을 무시하셔도 됩니다.')
            ->line('인증 링크는 60분 후에 만료됩니다.')
            ->salutation('감사합니다, HOSTYLE 팀');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
