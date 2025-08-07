<?php

namespace App\Filament\Widgets\Provider;

use App\Models\DocumentStatus;
use App\Models\ProviderDocument;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Provider Document Manager Widget
 *
 * This is the main document management interface for providers on their dashboard.
 * It displays all required documents for the authenticated provider, excluding
 * those marked as 'No Aplica', and provides functionality to upload files
 * and track document status through the approval workflow.
 *
 * The widget allows providers to:
 * - View their required documents and current status
 * - Upload files for pending or rejected documents
 * - View previously uploaded files
 * - Track rejection comments and feedback
 * - Monitor document approval progress
 */
class ProviderDocumentManagerWidget extends BaseWidget
{
    /**
     * Widget configuration to span the full width of the dashboard.
     *
     * This ensures the document manager takes up the full available space,
     * providing optimal viewing and interaction space for document management.
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * User-friendly heading displayed at the top of the widget.
     *
     * Clearly identifies this section as the provider's personal document
     * requirements, using Spanish for consistent localization.
     */
    protected static ?string $heading = 'Mis Documentos Requeridos 2';

    /**
     * Configure the document management table for providers.
     *
     * This method sets up the complete document management interface including
     * queries, columns, and actions for file upload and viewing functionality.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProviderDocument::query()
                    ->where('user_id', Auth::id())
                    ->whereHas('documentStatus', function ($query) {
                        $query->where('name', '!=', 'No Aplica');
                    })
                    ->with(['documentType', 'documentStatus'])
                    ->orderBy('document_type_id')
            )
            ->columns([
                // Document type name - what document is required
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Documento')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-m-document'),

                // Document description - explains what the document is for
                Tables\Columns\TextColumn::make('documentType.description')
                    ->label('Descripción')
                    ->limit(80)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 80 ? $state : null;
                    })
                    ->wrap(),

                // Current document status with dynamic styling
                Tables\Columns\TextColumn::make('documentStatus.name')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($record) => $record->documentStatus?->color ?? 'gray')
                    ->icon(fn ($record) => $record->documentStatus?->icon),

                // Upload timestamp - when the document was last uploaded
                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('Subido')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('No subido')
                    ->sortable(),

                // Expiration date for completed documents
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Vence')
                    ->date('d/m/Y')
                    ->placeholder('No expira')
                    ->color(fn ($state) => $state && now()->diffInDays($state) < 30 ? 'warning' : null),

                // Rejection comments - feedback for rejected documents
                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Comentarios')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return $state && strlen($state) > 50 ? $state : null;
                    })
                    ->placeholder('Sin comentarios')
                    ->visible(fn ($record) => ! empty($record->rejection_reason))
                    ->color('danger')
                    ->icon('heroicon-m-exclamation-triangle'),
            ])
            ->filters([
                // Filter by document status for easier management
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(DocumentStatus::pluck('name', 'id'))
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (isset($data['value']) && $data['value'] !== '') {
                            return $query->where('document_status_id', $data['value']);
                        }

                        return $query;
                    })
                    ->preload(),
            ])
            ->actions([
                // View uploaded file action - only visible when file exists
                Tables\Actions\Action::make('view')
                    ->label('Ver Archivo')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn (\Illuminate\Database\Eloquent\Model $record): bool => ! empty($record->file_path))
                    ->url(fn (\Illuminate\Database\Eloquent\Model $record): string => asset('storage/'.$record->file_path))
                    ->openUrlInNewTab()
                    ->tooltip('Abrir archivo en nueva pestaña'),

                // Upload file action - for pending or rejected documents
                Tables\Actions\Action::make('upload')
                    ->label('Subir Archivo')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->visible(function (\Illuminate\Database\Eloquent\Model $record): bool {
                        $statusName = $record->documentStatus?->name;

                        return in_array($statusName, ['Pendiente', 'Rechazado']);
                    })
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('Seleccionar archivo')
                            ->required()
                            ->acceptedFileTypes(function (\Illuminate\Database\Eloquent\Model $record) {
                                $allowedTypes = $record->documentType->allowed_file_types ?? [];
                                $mimeTypes = [];
                                foreach ($allowedTypes as $type) {
                                    $mimeTypes[] = match (strtolower($type)) {
                                        'pdf' => 'application/pdf',
                                        'jpg', 'jpeg' => 'image/jpeg',
                                        'png' => 'image/png',
                                        'gif' => 'image/gif',
                                        'doc' => 'application/msword',
                                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        default => "application/{$type}",
                                    };
                                }

                                return $mimeTypes;
                            })
                            ->maxSize(10240) // 10MB max file size
                            ->disk('public')
                            ->directory('provider-documents')
                            ->visibility('public')
                            ->helperText(function (\Illuminate\Database\Eloquent\Model $record) {
                                $types = implode(', ', $record->documentType->allowed_file_types ?? []);

                                return "Tipos permitidos: {$types}. Tamaño máximo: 10MB.";
                            }),
                    ])
                    ->action(function (\Illuminate\Database\Eloquent\Model $record, array $data): void {
                        try {
                            $filePath = $data['file'];

                            // Find the 'En Revisión' status ID
                            $reviewStatusId = DocumentStatus::where('name', 'En Revisión')->firstOrFail()->id;

                            // Calculate expiration date if document type has validity period
                            $expiresAt = null;
                            if ($record->documentType->validity_days > 0) {
                                $expiresAt = now()->addDays($record->documentType->validity_days);
                            }

                            // Update the provider document record with new file and status
                            $record->update([
                                'file_path' => $filePath,
                                'uploaded_at' => now(),
                                'document_status_id' => $reviewStatusId,
                                'expires_at' => $expiresAt,
                                'rejection_reason' => null, // Clear previous rejection reason
                            ]);

                            // Send success notification to the provider
                            Notification::make()
                                ->success()
                                ->title('Archivo subido exitosamente')
                                ->body("El documento '{$record->documentType->name}' ha sido enviado para revisión.")
                                ->icon('heroicon-o-check-circle')
                                ->duration(5000)
                                ->send();

                        } catch (\Exception $e) {
                            // Send error notification
                            Notification::make()
                                ->danger()
                                ->title('Error al subir archivo')
                                ->body('Ocurrió un error: '.$e->getMessage())
                                ->icon('heroicon-o-exclamation-triangle')
                                ->duration(5000)
                                ->send();
                        }
                    })
                    ->modalHeading(fn (\Illuminate\Database\Eloquent\Model $record): string => "Subir: {$record->documentType->name}")
                    ->modalSubmitActionLabel('Subir Documento')
                    ->modalWidth('md'),
            ])
            ->bulkActions([
                // No bulk actions to prevent accidental operations
            ])
            ->emptyStateHeading('Sin documentos requeridos')
            ->emptyStateDescription('No tienes documentos pendientes en este momento.')
            ->emptyStateIcon('heroicon-o-document-check')
            ->defaultPaginationPageOption(10)
            ->poll('60s'); // Auto-refresh every minute for status updates
    }
}
