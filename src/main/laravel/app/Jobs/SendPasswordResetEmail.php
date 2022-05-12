<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class SendPasswordResetEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $subject, $body;

    public function __construct(User $user, $subject = null, $body = null)
    {
        $this->user = $user;
        $this->subject = $subject ?? 'Reset password link';
        $this->body = $body ?? 'Please Don\'t share this link';
    }

    public function handle()
    {
        ResetPassword::$toMailCallback = function ($notifiable, $token) {
            $url =  url("/reset-password?token=$token&email=$notifiable->email");

            return $this->buildMailMessage($url);
        };

        Password::sendResetLink(
            ['email' => $this->user->email]
        );
    }

    public function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get($this->subject))
            ->line(Lang::get($this->body))
            ->action(Lang::get('Change Password'), $url)
            ->line(Lang::get('This password change link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]));
    }
}
