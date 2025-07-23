<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;

class CustomResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    public string $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
        
        // Set up queue connection
        $this->onQueue('default');
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable): string
    {
        if (isset($this->url)) {
            return $this->url;
        }

        // For Filament, use the Filament panel reset URL
        $panelId = Config::get('filament.default_panel', 'admin');
        
        return url(route('filament.' . $panelId . '.auth.password-reset.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = $this->resetUrl($notifiable);
        
        return (new MailMessage)
            ->subject('Recuperar Contraseña - Grupo Costeño')
            ->view('emails.password-reset', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'url' => $resetUrl, // Para compatibilidad con el template anterior
                'token' => $this->token,
                'expireMinutes' => Config::get('auth.passwords.users.expire', 60),
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ];
    }
}