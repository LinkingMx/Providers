<?php

namespace App\Filament\Widgets\Admin;

use App\Models\ProviderDocument;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Widget to display provider documents pending approval for admin users
 */
class PendingDocumentsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Documentos Pendientes de Aprobación';

    protected int|string|array $columnSpan = 'full';

    /**
     * Only show this widget to admin users
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['Admin', 'super_admin']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProviderDocument::query()
                    ->whereHas('documentStatus', function (Builder $query) {
                        $query->where('name', 'En Revisión');
                    })
                    ->with(['user', 'documentType', 'documentStatus'])
                    ->latest('updated_at')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('documentType.name')
                    ->label('Tipo de Documento')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('documentStatus.name')
                    ->label('Estado')
                    ->color(fn (string $state): string => match ($state) {
                        'En Revisión' => 'warning',
                        'Aprobado' => 'success',
                        'Rechazado' => 'danger',
                        'Pendiente' => 'gray',
                        default => 'secondary',
                    }),

                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('file_path')
                    ->label('Documento')
                    ->formatStateUsing(fn (?string $state): string => $state ? '📄 Ver Archivo' : 'Sin archivo')
                    ->url(fn (ProviderDocument $record): ?string => $record->file_path ? asset('storage/'.$record->file_path) : null
                    )
                    ->openUrlInNewTab()
                    ->color('primary'),

                TextColumn::make('rejection_reason')
                    ->label('Notas')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }

                        return $state;
                    }),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Aprobar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ProviderDocument $record) {
                        $approvedStatus = \App\Models\DocumentStatus::where('name', 'Aprobado')->first();
                        if ($approvedStatus) {
                            $record->update([
                                'document_status_id' => $approvedStatus->id,
                            ]);

                            Notification::make()
                                ->title('Documento aprobado exitosamente')
                                ->success()
                                ->send();
                        }
                    }),

                Action::make('reject')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('rejection_notes')
                            ->label('Motivo del rechazo')
                            ->required()
                            ->rows(3)
                            ->placeholder('Explique por qué se rechaza este documento...'),
                    ])
                    ->action(function (ProviderDocument $record, array $data) {
                        $rejectedStatus = \App\Models\DocumentStatus::where('name', 'Rechazado')->first();
                        if ($rejectedStatus) {
                            $record->update([
                                'document_status_id' => $rejectedStatus->id,
                                'rejection_reason' => $data['rejection_notes'],
                            ]);

                            Notification::make()
                                ->title('Documento rechazado')
                                ->body('Se ha notificado al proveedor sobre el rechazo.')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('No hay documentos pendientes')
            ->emptyStateDescription('Todos los documentos han sido procesados.')
            ->emptyStateIcon('heroicon-o-document-check')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }
}
