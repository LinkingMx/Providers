<?php

namespace App\Console\Commands;

use App\Mail\DocumentRejected;
use App\Models\ProviderDocument;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestDocumentRejectedEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-document-rejected {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test document rejected email template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Sending test document rejected email to: {$email}");
        
        // Create test data
        $user = new User();
        $user->name = 'Proveedor de Prueba';
        $user->email = $email;
        
        // Create a test document
        $document = new ProviderDocument();
        $document->setRelation('documentType', (object)[
            'name' => 'RFC - Registro Federal de Contribuyentes'
        ]);
        
        $rejectionReason = "El documento presentado no es legible. Por favor, suba una copia más clara del documento donde se puedan leer todos los datos correctamente. Asegúrese de que el documento esté actualizado y vigente.";
        
        try {
            Mail::to($email)->send(new DocumentRejected(
                $user,
                $document,
                $rejectionReason
            ));
            
            $this->info('✅ Test email sent successfully!');
            $this->info("Please check the inbox for: {$email}");
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email');
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}