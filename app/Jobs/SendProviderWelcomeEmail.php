<?php

namespace App\Jobs;

use App\Mail\ProviderWelcomeMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendProviderWelcomeEmail implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Verificar que el usuario tenga el rol Provider
            if (!$this->user->hasRole('Provider')) {
                Log::warning('SendProviderWelcomeEmail: Usuario no tiene rol Provider', [
                    'user_id' => $this->user->id,
                    'email' => $this->user->email,
                ]);
                return;
            }

            // Cargar las relaciones necesarias
            $this->user->load(['providerProfile', 'branches', 'roles']);

            // Enviar el correo de bienvenida
            Mail::to($this->user->email)->send(new ProviderWelcomeMail($this->user));

            Log::info('SendProviderWelcomeEmail: Correo enviado exitosamente', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
            ]);

        } catch (\Exception $e) {
            Log::error('SendProviderWelcomeEmail: Error al enviar correo', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-lanzar la excepción para que el job sea reintentado
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendProviderWelcomeEmail: Job falló después de todos los intentos', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
