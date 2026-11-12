<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordPetugas extends ResetPassword
{
    protected function resetUrl($notifiable): string
    {
        return route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Atur ulang kata sandi panel petugas — SMP PGRI 4 Waru')
            ->greeting('Permintaan atur ulang kata sandi')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun petugas sekolah yang terhubung dengan email ini.')
            ->action('Buat kata sandi baru', $this->resetUrl($notifiable))
            ->line('Tautan ini berlaku selama 60 menit dan hanya bisa dipakai satu kali.')
            ->line('Kalau Anda tidak meminta perubahan kata sandi, abaikan email ini. Kata sandi lama tetap berlaku.');
    }
}
