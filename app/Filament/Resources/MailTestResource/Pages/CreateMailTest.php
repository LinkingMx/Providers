<?php

namespace App\Filament\Resources\MailTestResource\Pages;

use App\Filament\Resources\MailTestResource;
use App\Jobs\SendTestMailJob;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMailTest extends CreateRecord
{
    protected static string $resource = MailTestResource::class;

    /**
     * Override the redirect URL to go back to the index page after creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle the creation and automatically dispatch the test job
     */
    protected function afterCreate(): void
    {
        try {
            $record = $this->record;
            $testUser = $record->test_type === 'provider_welcome' ? $record->user : null;
            
            // Agregar evento de creación
            $record->addEvent('test_created', [
                'test_type' => $record->test_type,
                'recipient' => $record->recipient_email,
            ]);

            // Dispatch del job
            SendTestMailJob::dispatch(
                $record,
                $record->test_type,
                $record->recipient_email,
                $testUser
            );

            $record->addEvent('job_dispatched');

            Notification::make()
                ->success()
                ->title('Prueba de Correo Creada')
                ->body('La prueba ha sido enviada a la cola de procesamiento.')
                ->duration(5000)
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error al Crear Prueba')
                ->body('Error: ' . $e->getMessage())
                ->duration(10000)
                ->send();
        }
    }

    /**
     * Mutate form data before creation
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // Quien ejecuta la prueba
        $data['status'] = 'pending';
        
        return $data;
    }

    /**
     * Custom page title
     */
    public function getTitle(): string
    {
        return 'Nueva Prueba de Correo';
    }

    /**
     * Custom page subtitle
     */
    public function getSubheading(): string
    {
        return 'Configura y ejecuta una nueva prueba de correo';
    }
}
