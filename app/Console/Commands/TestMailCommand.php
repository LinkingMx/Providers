<?php

namespace App\Console\Commands;

use App\Jobs\SendTestMailJob;
use App\Models\MailTest;
use App\Models\User;
use Illuminate\Console\Command;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test
                            {type : Tipo de prueba (smtp|provider-welcome)}
                            {email : Email de destino}
                            {--user-id= : ID del usuario para prueba de provider-welcome}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba para verificar la configuración SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->argument('email');
        $userId = $this->option('user-id');

        // Validar tipo
        if (!in_array($type, ['smtp', 'provider-welcome'])) {
            $this->error('Tipo de prueba no válido. Use: smtp o provider-welcome');
            return 1;
        }

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email no válido');
            return 1;
        }

        // Para provider-welcome, necesitamos un usuario
        $testUser = null;
        if ($type === 'provider-welcome') {
            if (!$userId) {
                $this->error('Para prueba provider-welcome debe especificar --user-id');
                return 1;
            }

            $testUser = User::find($userId);
            if (!$testUser) {
                $this->error("Usuario con ID {$userId} no encontrado");
                return 1;
            }

            if (!$testUser->hasRole('Provider')) {
                $this->warn("El usuario {$testUser->name} no tiene rol Provider");
            }
        }

        $this->info("Preparando prueba de correo...");
        $this->info("Tipo: {$type}");
        $this->info("Destinatario: {$email}");
        if ($testUser) {
            $this->info("Usuario de prueba: {$testUser->name} ({$testUser->email})");
        }

        try {
            // Buscar o crear usuario admin
            $adminUser = User::first();
            if (!$adminUser) {
                $this->error('No hay usuarios en el sistema. Cree un usuario primero.');
                return 1;
            }

            // Crear registro de prueba
            $mailTest = MailTest::create([
                'user_id' => $adminUser->id,
                'test_type' => $type === 'smtp' ? 'smtp_test' : 'provider_welcome',
                'recipient_email' => $email,
                'status' => 'pending',
            ]);

            $this->info("Prueba creada con ID: {$mailTest->id}");

            // Despachar job
            SendTestMailJob::dispatch(
                $mailTest,
                $mailTest->test_type,
                $email,
                $testUser
            );

            $this->info("✅ Correo de prueba enviado a la cola de procesamiento");
            $this->info("💡 Ejecute 'php artisan queue:work' para procesar la cola");
            $this->info("🔍 Revise el resultado en /admin/mail-tests o use: php artisan mail:status {$mailTest->id}");

            return 0;

        } catch (\Exception $e) {
            $this->error("Error al enviar correo de prueba: " . $e->getMessage());
            return 1;
        }
    }
}
