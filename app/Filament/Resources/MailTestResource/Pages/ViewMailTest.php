<?php

namespace App\Filament\Resources\MailTestResource\Pages;

use App\Filament\Resources\MailTestResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMailTest extends ViewRecord
{
    protected static string $resource = MailTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Actualizar')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->refreshFormData()),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de la Prueba')
                    ->schema([
                        Infolists\Components\TextEntry::make('test_type')
                            ->label('Tipo de Prueba')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'provider_welcome' => 'Correo de Bienvenida de Proveedor',
                                'smtp_test' => 'Prueba SMTP Básica',
                                default => $state,
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'provider_welcome' => 'info',
                                'smtp_test' => 'gray',
                                default => 'primary',
                            }),

                        Infolists\Components\TextEntry::make('recipient_email')
                            ->label('Destinatario')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Usuario de Prueba')
                            ->placeholder('No aplica'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Estado')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Pendiente',
                                'sent' => 'Enviado Exitosamente',
                                'failed' => 'Falló',
                                default => $state,
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'sent' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('sent_at')
                            ->label('Fecha de Envío')
                            ->dateTime()
                            ->placeholder('No enviado'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Error')
                    ->schema([
                        Infolists\Components\TextEntry::make('error_message')
                            ->label('Mensaje de Error')
                            ->color('danger')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => $record->status === 'failed' && !empty($record->error_message)),

                Infolists\Components\Section::make('Log de Eventos')
                    ->description('Registro detallado de todos los eventos durante el procesamiento')
                    ->schema([
                        Infolists\Components\ViewEntry::make('events_log')
                            ->label('')
                            ->view('filament.mail-test.events-log')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => !empty($record->events_log)),

                Infolists\Components\Section::make('Datos del Correo')
                    ->description('Información técnica sobre el correo enviado')
                    ->schema([
                        Infolists\Components\TextEntry::make('mail_data')
                            ->label('Datos Técnicos')
                            ->formatStateUsing(fn ($state): string => 
                                is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 'No disponible'
                            )
                            ->extraAttributes(['class' => 'font-mono text-sm'])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record): bool => !empty($record->mail_data))
                    ->collapsed(),
            ]);
    }

    /**
     * Custom page title
     */
    public function getTitle(): string
    {
        return "Prueba de Correo #{$this->record->id}";
    }
}
