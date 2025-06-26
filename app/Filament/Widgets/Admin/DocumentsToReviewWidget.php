<?php

namespace App\Filament\Widgets\Admin;

use App\Filament\Resources\UserResource;
use App\Models\ProviderDocument;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DocumentsToReviewWidget extends BaseWidget
{
    /**
     * Widget configuration
     */
    protected static ?string $heading = 'Documentos Pendientes de Revisión';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    /**
     * Only allow access to administrators and super_admins
     */
    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    /**
     * Configure the table for documents awaiting review.
     *
     * This widget serves as the admin's to-do list, showing all documents
     * that are currently in 'En Revisión' status and need administrative attention.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query ProviderDocument records with 'En Revisión' status
                // Includes eager loading for efficient database queries
                ProviderDocument::query()
                    ->whereHas('documentStatus', function (Builder $query) {
                        $query->where('name', 'En Revisión');
                    })
                    ->with(['user', 'documentType', 'documentStatus'])
                    ->orderBy('uploaded_at', 'asc') // Oldest first for FIFO processing
            )
            ->columns([
                // Provider name - who submitted the document
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-m-user'),

                // Document type - what type of document needs review
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Tipo de Documento')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                // Upload timestamp - when the document was submitted
                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('Subido')
                    ->dateTime()
                    ->sortable()
                    ->since() // Shows relative time (e.g., "2 days ago")
                    ->placeholder('No subido')
                    ->icon('heroicon-m-clock'),

                // Current status - confirmation of review status
                Tables\Columns\TextColumn::make('documentStatus.name')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($record) => $record->documentStatus?->color ?? 'gray')
                    ->icon(fn ($record) => $record->documentStatus?->icon),
            ])
            ->filters([
                // Filter by provider for focused review sessions
                Tables\Filters\SelectFilter::make('user')
                    ->label('Proveedor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                // Filter by document type for specialized review
                Tables\Filters\SelectFilter::make('documentType')
                    ->label('Tipo de Documento')
                    ->relationship('documentType', 'name')
                    ->preload(),
            ])
            ->actions([
                // Primary action: Review the document by going to user's edit page
                Tables\Actions\Action::make('review')
                    ->label('Revisar')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('primary')
                    ->url(fn (ProviderDocument $record): string => UserResource::getUrl('edit', ['record' => $record->user_id])
                    )
                    ->openUrlInNewTab(false)
                    ->tooltip('Ir al perfil del proveedor para revisar el documento'),
            ])
            ->bulkActions([
                // No bulk actions to prevent accidental mass operations
            ])
            ->recordClasses(fn (ProviderDocument $record) =>
                // Highlight rows where documents are older than 2 days
                $record->uploaded_at && $record->uploaded_at->lt(now()->subDays(2))
                    ? 'bg-red-50 border-l-4 border-l-red-500 dark:bg-red-950/20'
                    : null
            )
            ->emptyStateHeading('¡Excelente!')
            ->emptyStateDescription('No hay documentos pendientes de revisión en este momento.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->defaultPaginationPageOption(10)
            ->poll('30s'); // Auto-refresh every 30 seconds for real-time updates
    }

    /**
     * Get the table record key for proper identification.
     */
    public function getTableRecordKey(mixed $record): string
    {
        return (string) $record->getKey();
    }
}
