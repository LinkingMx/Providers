<?php

namespace App\Filament\Resources\MailTestResource\Pages;

use App\Filament\Resources\MailTestResource;
use Filament\Resources\Pages\ListRecords;

class ListMailTests extends ListRecords
{
    protected static string $resource = MailTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Nueva Prueba'),
        ];
    }

    /**
     * Custom page title
     */
    public function getTitle(): string
    {
        return 'Pruebas de Correo';
    }

    /**
     * Custom page subtitle
     */
    public function getSubheading(): string
    {
        return 'Monitorea y ejecuta pruebas de correo para verificar la configuración SMTP';
    }
}
