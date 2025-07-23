<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EmailPreviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:preview {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra URLs para previsualizar templates de correo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $baseUrl = config('app.url');

        $this->info('=== 📧 Previews de Templates de Correo ===');
        $this->line('');

        if (!$type || $type === 'all') {
            $this->line('🌐 <fg=blue>Página Principal de Previews:</>', 'info');
            $this->line("   {$baseUrl}/email-previews");
            $this->line('');

            $this->line('🎉 <fg=green>Correo de Bienvenida:</>', 'info');
            $this->line("   {$baseUrl}/test-provider-welcome");
            $this->line('');

            $this->line('🔐 <fg=yellow>Recuperación de Contraseña:</>', 'info');
            $this->line("   {$baseUrl}/test-password-reset");
            $this->line('');

            $this->line('👥 <fg=cyan>Con usuarios reales:</>', 'info');
            $users = \App\Models\User::take(3)->get();
            foreach ($users as $user) {
                $this->line("   Password Reset: {$baseUrl}/test-password-reset/{$user->id} ({$user->name})");
                if ($user->hasRole('Provider')) {
                    $this->line("   Send Welcome: {$baseUrl}/test-send-welcome/{$user->id} ({$user->name})");
                }
            }
        } elseif ($type === 'welcome') {
            $this->line('🎉 <fg=green>Correo de Bienvenida:</>', 'info');
            $this->line("   {$baseUrl}/test-provider-welcome");
        } elseif ($type === 'password') {
            $this->line('🔐 <fg=yellow>Recuperación de Contraseña:</>', 'info');
            $this->line("   {$baseUrl}/test-password-reset");
        } else {
            $this->error("Tipo no válido. Use: welcome, password, o all");
            return 1;
        }

        $this->line('');
        $this->info('💡 Abre las URLs en tu navegador para ver los previews');
        $this->info('💡 Los templates usan el mismo diseño que los correos reales');

        return 0;
    }
}
