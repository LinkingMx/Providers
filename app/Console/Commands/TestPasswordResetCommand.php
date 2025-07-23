<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Console\Command;

class TestPasswordResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba de recuperación de contraseña';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }

        try {
            // Generar un token de prueba
            $token = 'test-' . bin2hex(random_bytes(32));
            
            // Crear y enviar la notificación
            $notification = new CustomResetPasswordNotification($token);
            $user->notify($notification);

            $this->info("✅ Correo de prueba de password reset enviado a: {$user->email}");
            $this->info("💡 Ejecuta 'php artisan queue:work --once' para procesar el correo");
            
            return 0;

        } catch (\Exception $e) {
            $this->error("Error al enviar el correo: " . $e->getMessage());
            return 1;
        }
    }
}
