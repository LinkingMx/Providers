<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderDocumentResource\Pages;
use App\Models\DocumentStatus;
use App\Models\ProviderDocument;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/**
 * Provider Document Resource
 *
 * This resource allows administrators to review, approve, reject, and manage
 * all documents submitted by providers. It provides full workflow management
 * for document compliance and approval processes.
 */
class ProviderDocumentResource extends Resource
{
    protected static ?string $model = ProviderDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Documentos de Proveedores';

    protected static ?string $modelLabel = 'Documento de Proveedor';

    protected static ?string $pluralModelLabel = 'Documentos de Proveedores';

    protected static ?string $navigationGroup = 'Gestión de Proveedores';

    protected static ?int $navigationSort = 3;

    /**
     * Restrict access to users with appropriate permissions
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(['super_admin', 'Admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Documento')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Proveedor')
                            ->options(
                                User::whereHas('roles', function ($query) {
                                    $query->where('name', 'Provider');
                                })->pluck('name', 'id')
                            )
                            ->required()
                            ->searchable()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\Select::make('document_type_id')
                            ->label('Tipo de Documento')
                            ->relationship('documentType', 'name')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),

                        Forms\Components\Select::make('document_status_id')
                            ->label('Estado del Documento')
                            ->options(DocumentStatus::pluck('name', 'id'))
                            ->required(),
                    ]),

                Forms\Components\Section::make('Información del Archivo')
                    ->schema([
                        Forms\Components\TextInput::make('file_path')
                            ->label('Ruta del Archivo')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DateTimePicker::make('uploaded_at')
                            ->label('Fecha de Subida')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Fecha de Expiración')
                            ->helperText('Se calcula automáticamente según el tipo de documento'),
                    ]),

                Forms\Components\Section::make('Revisión Administrativa')
                    ->schema([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo de Rechazo')
                            ->rows(3)
                            ->helperText('Proporcione una explicación clara del motivo del rechazo para documentos rechazados'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Proveedor')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Tipo de Documento')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('documentStatus.name')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($record) => $record->documentStatus?->color ?? 'gray')
                    ->icon(fn ($record) => $record->documentStatus?->icon),

                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('Fecha de Subida')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No subido'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expira')
                    ->date()
                    ->sortable()
                    ->placeholder('No expira'),

                Tables\Columns\IconColumn::make('file_path')
                    ->label('Archivo')
                    ->boolean()
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => ! empty($record->file_path)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('document_status_id')
                    ->label('Estado')
                    ->options(DocumentStatus::pluck('name', 'id'))
                    ->preload(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Proveedor')
                    ->options(
                        User::whereHas('roles', function ($query) {
                            $query->where('name', 'Provider');
                        })->pluck('name', 'id')
                    )
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_file')
                    ->label('Ver Archivo')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => ! empty($record->file_path))
                    ->url(fn ($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->documentStatus->name !== 'Aprobado')
                    ->requiresConfirmation()
                    ->modalHeading('Aprobar Documento')
                    ->modalDescription('¿Está seguro de que desea aprobar este documento?')
                    ->action(function ($record) {
                        $approvedStatus = DocumentStatus::where('name', 'Aprobado')->first();
                        if ($approvedStatus) {
                            $record->update([
                                'document_status_id' => $approvedStatus->id,
                                'rejection_reason' => null,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Documento Aprobado')
                                ->body("El documento '{$record->documentType->name}' ha sido aprobado.")
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->documentStatus->name !== 'Rechazado')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo del Rechazo')
                            ->required()
                            ->rows(3)
                            ->helperText('Proporcione una explicación clara del motivo del rechazo'),
                    ])
                    ->action(function ($record, array $data) {
                        $rejectedStatus = DocumentStatus::where('name', 'Rechazado')->first();
                        if ($rejectedStatus) {
                            $record->update([
                                'document_status_id' => $rejectedStatus->id,
                                'rejection_reason' => $data['rejection_reason'],
                            ]);

                            Notification::make()
                                ->warning()
                                ->title('Documento Rechazado')
                                ->body("El documento '{$record->documentType->name}' ha sido rechazado.")
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('uploaded_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderDocuments::route('/'),
            'edit' => Pages\EditProviderDocument::route('/{record}/edit'),
        ];
    }
}
