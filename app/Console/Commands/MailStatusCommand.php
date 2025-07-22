<?php

namespace App\Console\Commands;

use App\Models\MailTest;
use Illuminate\Console\Command;

class MailStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:status
                            {id? : ID específico de prueba de correo}
                            {--recent=10 : Mostrar últimas N pruebas}
                            {--failed : Mostrar solo las pruebas fallidas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra el estado de las pruebas de correo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $recent = $this->option('recent');
        $failedOnly = $this->option('failed');

        if ($id) {
            $this->showSpecificTest($id);
        } else {
            $this->showRecentTests($recent, $failedOnly);
        }

        return 0;
    }

    private function showSpecificTest($id)
    {
        $test = MailTest::with('user')->find($id);

        if (!$test) {
            $this->error("Prueba con ID {$id} no encontrada");
            return;
        }

        $this->info("=== Detalles de Prueba de Correo #{$test->id} ===");
        $this->line("Estado: " . $this->getStatusColor($test->status));
        $this->line("Tipo: {$test->test_type}");
        $this->line("Destinatario: {$test->recipient_email}");
        $this->line("Usuario: {$test->user->name} ({$test->user->email})");
        $this->line("Creado: {$test->created_at->format('Y-m-d H:i:s')}");
        $this->line("Actualizado: {$test->updated_at->format('Y-m-d H:i:s')}");

        if ($test->sent_at) {
            $this->line("Enviado: {$test->sent_at->format('Y-m-d H:i:s')}");
        }

        if ($test->error_message) {
            $this->error("Error: {$test->error_message}");
        }

        // Mostrar eventos
        if (!empty($test->events)) {
            $this->line("\n--- Eventos ---");
            foreach ($test->events as $event) {
                $timestamp = \Carbon\Carbon::parse($event['timestamp'])->format('H:i:s');
                $level = $event['level'] ?? 'info';
                $message = $event['message'] ?? '';
                
                $color = match($level) {
                    'error' => 'red',
                    'warning' => 'yellow',
                    'success' => 'green',
                    default => 'white'
                };

                $this->line("<fg={$color}>[{$timestamp}] {$level}: {$message}</>");
            }
        }
    }

    private function showRecentTests($limit, $failedOnly)
    {
        $query = MailTest::with('user')->orderBy('created_at', 'desc');

        if ($failedOnly) {
            $query->where('status', 'failed');
        }

        $tests = $query->limit($limit)->get();

        if ($tests->isEmpty()) {
            $this->info($failedOnly ? 'No hay pruebas fallidas' : 'No hay pruebas de correo');
            return;
        }

        $title = $failedOnly ? "Últimas {$limit} Pruebas Fallidas" : "Últimas {$limit} Pruebas de Correo";
        $this->info("=== {$title} ===");

        $headers = ['ID', 'Estado', 'Tipo', 'Destinatario', 'Usuario', 'Fecha'];
        $rows = [];

        foreach ($tests as $test) {
            $rows[] = [
                $test->id,
                $test->status,
                $test->test_type,
                $test->recipient_email,
                $test->user->name,
                $test->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $this->table($headers, $rows);

        $this->line("\n💡 Use 'php artisan mail:status {ID}' para ver detalles específicos");
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'sent' => "<fg=green>{$status}</>",
            'failed' => "<fg=red>{$status}</>",
            'pending' => "<fg=yellow>{$status}</>",
            'processing' => "<fg=blue>{$status}</>",
            default => $status
        };
    }
}
