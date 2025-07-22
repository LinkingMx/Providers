<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MailTestResource\Pages;
use App\Jobs\SendTestMailJob;
use App\Models\MailTest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MailTestResource extends Resource
{
    protected static ?string $model = MailTest::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Pruebas de Correo';

    protected static ?string $modelLabel = 'Prueba de Correo';

    protected static ?string $pluralModelLabel = 'Pruebas de Correo';

    protected static ?string $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Configuración de Prueba')
                    ->description('Configura los parámetros para la prueba de correo')
                    ->schema([
                        Forms\Components\Select::make('test_type')
                            ->label('Tipo de Prueba')
                            ->options([
                                'provider_welcome' => 'Correo de Bienvenida de Proveedor',
                                'smtp_test' => 'Prueba SMTP Básica',
                            ])
                            ->required()
                            ->default('provider_welcome')
                            ->live()
                            ->helperText('Selecciona el tipo de correo que quieres probar'),

                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Correo de Destino')
                            ->email()
                            ->required()
                            ->helperText('Dirección de correo donde se enviará la prueba'),

                        Forms\Components\Select::make('user_id')
                            ->label('Usuario de Prueba')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Forms\Get $get): bool => $get('test_type') === 'provider_welcome')
                            ->visible(fn (Forms\Get $get): bool => $get('test_type') === 'provider_welcome')
                            ->helperText('Usuario que se usará como datos de prueba en el correo'),
                    ]),

                Forms\Components\Section::make('Estado de la Prueba')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pending' => 'Pendiente',
                                'sent' => 'Enviado',
                                'failed' => 'Fallido',
                            ])
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Textarea::make('error_message')
                            ->label('Mensaje de Error')
                            ->disabled()
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),

                        Forms\Components\Textarea::make('events_log')
                            ->label('Log de Eventos')
                            ->formatStateUsing(fn ($state): string => 
                                is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ''
                            )
                            ->disabled()
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),
                    ])
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('test_type')
                    ->label('Tipo de Prueba')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'provider_welcome' => 'info',
                        'smtp_test' => 'gray',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'provider_welcome' => 'Bienvenida Proveedor',
                        'smtp_test' => 'SMTP Básica',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('recipient_email')
                    ->label('Destinatario')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario de Prueba')
                    ->placeholder('N/A')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'sent' => 'Enviado',
                        'failed' => 'Fallido',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Enviado')
                    ->dateTime()
                    ->placeholder('No enviado')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'sent' => 'Enviado',
                        'failed' => 'Fallido',
                    ]),

                Tables\Filters\SelectFilter::make('test_type')
                    ->label('Tipo de Prueba')
                    ->options([
                        'provider_welcome' => 'Bienvenida Proveedor',
                        'smtp_test' => 'SMTP Básica',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('send_test')
                    ->label('Enviar Prueba')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn (MailTest $record): bool => $record->status === 'pending')
                    ->action(function (MailTest $record) {
                        try {
                            $testUser = $record->test_type === 'provider_welcome' ? $record->user : null;
                            
                            SendTestMailJob::dispatch(
                                $record,
                                $record->test_type,
                                $record->recipient_email,
                                $testUser
                            );

                            $record->addEvent('job_dispatched');

                            Notification::make()
                                ->success()
                                ->title('Prueba Enviada')
                                ->body('El correo de prueba ha sido enviado a la cola de procesamiento.')
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error al Enviar')
                                ->body('Error: ' . $e->getMessage())
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('retry_test')
                    ->label('Reintentar')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (MailTest $record): bool => $record->status === 'failed')
                    ->action(function (MailTest $record) {
                        try {
                            // Resetear estado
                            $record->update([
                                'status' => 'pending',
                                'error_message' => null,
                                'sent_at' => null,
                            ]);
                            $record->addEvent('test_reset_for_retry');

                            $testUser = $record->test_type === 'provider_welcome' ? $record->user : null;
                            
                            SendTestMailJob::dispatch(
                                $record,
                                $record->test_type,
                                $record->recipient_email,
                                $testUser
                            );

                            Notification::make()
                                ->success()
                                ->title('Reintento Enviado')
                                ->body('El correo de prueba ha sido reenviado a la cola.')
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error al Reintentar')
                                ->body('Error: ' . $e->getMessage())
                                ->send();
                        }
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('quick_smtp_test')
                    ->label('Prueba SMTP Rápida')
                    ->icon('heroicon-o-bolt')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('email')
                            ->label('Correo de Destino')
                            ->email()
                            ->required()
                            ->default(auth()->user()->email),
                    ])
                    ->action(function (array $data) {
                        try {
                            $mailTest = MailTest::create([
                                'user_id' => auth()->id(),
                                'test_type' => 'smtp_test',
                                'recipient_email' => $data['email'],
                                'status' => 'pending',
                            ]);

                            SendTestMailJob::dispatch(
                                $mailTest,
                                'smtp_test',
                                $data['email']
                            );

                            Notification::make()
                                ->success()
                                ->title('Prueba SMTP Enviada')
                                ->body("Correo de prueba enviado a {$data['email']}")
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Error en Prueba SMTP')
                                ->body('Error: ' . $e->getMessage())
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s'); // Auto-refresh cada 10 segundos
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMailTests::route('/'),
            'create' => Pages\CreateMailTest::route('/create'),
            'view' => Pages\ViewMailTest::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'failed')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'failed')->exists() ? 'danger' : null;
    }
}
