<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class CustomResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        parent::__construct($token);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(config('app.url').route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de Contraseña - Portal de Proveedores')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Recibiste este correo porque se solicitó una recuperación de contraseña para tu cuenta en el Portal de Proveedores de Grupo Costeño.')
            ->line('Este enlace de recuperación expirará en ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.')
            ->action('Restablecer Contraseña', $url)
            ->line('Si no solicitaste una recuperación de contraseña, puedes ignorar este correo.')
            ->line('**Aviso de Seguridad:** Por tu seguridad, nunca compartas este enlace con nadie más. Si sospechas que alguien más tiene acceso a tu cuenta, contáctanos inmediatamente.')
            ->salutation('Saludos,<br>Equipo de Grupo Costeño');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ];
    }
}