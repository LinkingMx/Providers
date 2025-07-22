<?php

namespace App\Jobs;

use App\Mail\ProviderWelcomeMail;
use App\Models\MailTest;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTestMailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1; // Solo 1 intento para pruebas

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public MailTest $mailTest,
        public string $testType,
        public string $recipientEmail,
        public ?User $testUser = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->mailTest->addEvent('job_started', [
                'test_type' => $this->testType,
                'recipient' => $this->recipientEmail,
            ]);

            Log::info('SendTestMailJob: Iniciando envío de correo de prueba', [
                'mail_test_id' => $this->mailTest->id,
                'test_type' => $this->testType,
                'recipient' => $this->recipientEmail,
            ]);

            switch ($this->testType) {
                case 'provider_welcome':
                    $this->sendProviderWelcomeTest();
                    break;
                case 'smtp_test':
                    $this->sendSmtpTest();
                    break;
                default:
                    throw new \Exception("Tipo de prueba no soportado: {$this->testType}");
            }

            $this->mailTest->markAsSent();

            Log::info('SendTestMailJob: Correo de prueba enviado exitosamente', [
                'mail_test_id' => $this->mailTest->id,
                'test_type' => $this->testType,
            ]);

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            $this->mailTest->markAsFailed($errorMessage);

            Log::error('SendTestMailJob: Error al enviar correo de prueba', [
                'mail_test_id' => $this->mailTest->id,
                'test_type' => $this->testType,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lanzar la excepción para que se marque como fallido
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->mailTest->markAsFailed($exception->getMessage());
        
        Log::error('SendTestMailJob: Job falló', [
            'mail_test_id' => $this->mailTest->id,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Enviar correo de bienvenida de prueba
     */
    private function sendProviderWelcomeTest(): void
    {
        if (!$this->testUser) {
            throw new \Exception('Usuario de prueba requerido para correo de bienvenida');
        }

        $this->mailTest->addEvent('preparing_provider_welcome', [
            'user_id' => $this->testUser->id,
            'user_name' => $this->testUser->name,
        ]);

        // Cargar relaciones necesarias
        $this->testUser->load(['providerProfile', 'branches', 'roles']);

        // Enviar el correo
        Mail::to($this->recipientEmail)->send(new ProviderWelcomeMail($this->testUser));

        $this->mailTest->addEvent('provider_welcome_sent');
    }

    /**
     * Enviar correo de prueba SMTP básico
     */
    private function sendSmtpTest(): void
    {
        $this->mailTest->addEvent('preparing_smtp_test');

        Mail::raw('Este es un correo de prueba para verificar la configuración SMTP.', function ($message) {
            $message->to($this->recipientEmail)
                ->subject('Prueba SMTP - Portal de Proveedores');
        });

        $this->mailTest->addEvent('smtp_test_sent');
    }
}
