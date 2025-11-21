<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;

class CustomVerifyEmail extends VerifyEmailNotification
{
    protected function verificationUrl($notifiable)
    {
        // nếu route của bạn tên là users.verification.verify
        return URL::temporarySignedRoute(
            'users.verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Xác minh tài khoản - Lyn Cosmetic & Spa')
            ->greeting('Xin chào ' . ($notifiable->name ?? 'bạn'))
            ->line('Cảm ơn bạn đã đăng ký tại Lyn Cosmetic & Spa.')
            ->line('Vui lòng bấm nút dưới đây để xác minh email.')
            ->action('Xác minh ngay', $this->verificationUrl($notifiable))
            ->salutation('Thân mến, Lyn Cosmetic & Spa');
    }
}
