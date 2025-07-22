<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class CheckMailConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:check-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la configuración de correo y muestra información del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Verificación de Configuración de Correo ===');

        // Configuración de correo
        $this->line("\n📧 Configuración de Mail:");
        $this->line("Default Mailer: " . config('mail.default'));
        $this->line("From Address: " . config('mail.from.address'));
        $this->line("From Name: " . config('mail.from.name'));

        // Configuración SMTP
        $this->line("\n📮 Configuración SMTP:");
        $smtpConfig = config('mail.mailers.smtp');
        $this->line("Host: " . ($smtpConfig['host'] ?? 'No configurado'));
        $this->line("Port: " . ($smtpConfig['port'] ?? 'No configurado'));
        $this->line("Username: " . ($smtpConfig['username'] ?? 'No configurado'));
        $this->line("Encryption: " . ($smtpConfig['encryption'] ?? 'No configurado'));

        // Variables de entorno críticas
        $this->line("\n🔧 Variables de Entorno:");
        $envVars = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME'
        ];

        foreach ($envVars as $var) {
            $value = env($var);
            if ($var === 'MAIL_PASSWORD') {
                $display = $value ? '***oculto***' : 'No configurado';
            } else {
                $display = $value ?? 'No configurado';
            }
            $this->line("{$var}: {$display}");
        }

        // Configuración de colas
        $this->line("\n⚡ Configuración de Colas:");
        $this->line("Default Queue Connection: " . config('queue.default'));
        $this->line("Queue Driver: " . config('queue.connections.' . config('queue.default') . '.driver'));

        // Estado del supervisor
        $this->line("\n🔍 Verificación del Sistema:");
        
        try {
            $queueStatus = shell_exec('ps aux | grep "queue:work" | grep -v grep');
            if ($queueStatus) {
                $this->info("✅ Procesos queue:work en ejecución");
                $this->line($queueStatus);
            } else {
                $this->warn("⚠️ No se encontraron procesos queue:work activos");
            }
        } catch (\Exception $e) {
            $this->warn("No se pudo verificar procesos queue:work");
        }

        try {
            $supervisorStatus = shell_exec('sudo supervisorctl status | grep laravel');
            if ($supervisorStatus) {
                $this->info("✅ Supervisor ejecutando procesos Laravel:");
                $this->line($supervisorStatus);
            } else {
                $this->warn("⚠️ No se encontraron procesos Laravel en Supervisor");
            }
        } catch (\Exception $e) {
            $this->warn("No se pudo verificar estado de Supervisor");
        }

        // Verificar permisos de storage
        $this->line("\n📁 Permisos de Almacenamiento:");
        $storagePath = storage_path();
        $logsPath = storage_path('logs');
        
        $this->line("Storage path: {$storagePath}");
        $this->line("Storage writable: " . (is_writable($storagePath) ? '✅ Sí' : '❌ No'));
        $this->line("Logs writable: " . (is_writable($logsPath) ? '✅ Sí' : '❌ No'));

        // Verificar archivos de log recientes
        $latestLog = $this->getLatestLogFile();
        if ($latestLog) {
            $this->line("Último log: {$latestLog}");
        }

        $this->line("\n💡 Comandos útiles para diagnóstico:");
        $this->line("- php artisan mail:test smtp correo@ejemplo.com");
        $this->line("- php artisan mail:status --recent=5");
        $this->line("- php artisan mail:status --failed");
        $this->line("- php artisan queue:work");
        $this->line("- php artisan config:cache");
        $this->line("- php artisan config:clear");

        return 0;
    }

    private function getLatestLogFile()
    {
        $logsPath = storage_path('logs');
        $files = glob($logsPath . '/laravel-*.log');
        
        if (empty($files)) {
            return null;
        }

        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return basename($files[0]);
    }
}
