<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\DocumentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentRequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'documentRequirements';

    // Spanish labels for the relation manager
    protected static ?string $title = 'Documentos Requeridos';

    protected static ?string $modelLabel = 'Documento';

    protected static ?string $pluralModelLabel = 'Documentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // This form is not used, management is done via table actions
                Forms\Components\Placeholder::make('info')
                    ->label('Información')
                    ->content('La gestión de documentos se realiza a través de las acciones de la tabla.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                // Document type name
                Tables\Columns\TextColumn::make('name')
                    ->label('Documento')
                    ->searchable()
                    ->weight('medium'),

                // **CORREGIDO**: Accede a la relación 'status' del modelo pivote
                Tables\Columns\TextColumn::make('pivot.status.name')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (Model $record) => $record->pivot?->status?->color ?? 'gray')
                    ->icon(fn (Model $record) => $record->pivot?->status?->icon),

                // Expiration date
                Tables\Columns\TextColumn::make('pivot.expires_at')
                    ->label('Vence')
                    ->date(),

                // Upload date
                Tables\Columns\TextColumn::make('pivot.uploaded_at')
                    ->label('Subido')
                    ->since(),
            ])
            ->filters([
                // **LA CORRECCIÓN PRINCIPAL ESTÁ AQUÍ**
                Tables\Filters\SelectFilter::make('document_status_id')
                    ->label('Filtrar por Estado')
                    // Cargamos las opciones manualmente
                    ->options(fn () => DocumentStatus::pluck('name', 'id'))
                    // Aplicamos la condición directamente a la tabla pivote
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        return $query->wherePivot('document_status_id', $data['value']);
                    }),
            ])
            ->headerActions([
                // No header actions needed
            ])
            ->actions([
                // View uploaded file
                Action::make('view')
                    ->label('Ver Archivo')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn (Model $record): bool => ! empty($record->pivot?->file_path))
                    ->url(fn (Model $record): string => Storage::url($record->pivot->file_path))
                    ->openUrlInNewTab(),

                // Change document status
                Action::make('change_status')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('document_status_id')
                            ->label('Nuevo Estado')
                            ->required()
                            ->options(function (Model $record) {
                                // **CORREGIDO**: Accede a la colección de la relación directamente
                                return $record->pivot?->status?->next_statuses->pluck('name', 'id');
                            })
                            ->live()
                            ->helperText('Solo se muestran los estados permitidos.'),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motivo del Rechazo')
                            ->visible(function (Get $get) {
                                if (! $statusId = $get('document_status_id')) {
                                    return false;
                                }

                                return DocumentStatus::find($statusId)?->name === 'Rechazado';
                            })
                            ->required(function (Get $get) {
                                if (! $statusId = $get('document_status_id')) {
                                    return false;
                                }

                                return DocumentStatus::find($statusId)?->name === 'Rechazado';
                            }),
                    ])
                    ->action(function (Model $record, array $data): void {
                        $newStatus = DocumentStatus::find($data['document_status_id']);
                        $updateData = [
                            'document_status_id' => $data['document_status_id'],
                            'rejection_reason' => $data['rejection_reason'] ?? null,
                        ];

                        if ($newStatus?->is_complete && $record->validity_days > 0) {
                            $updateData['expires_at'] = now()->addDays($record->validity_days);
                        }

                        $record->pivot->update($updateData);

                        Notification::make()->success()->title('Estado actualizado')->send();
                    }),
            ]);
    }
}
