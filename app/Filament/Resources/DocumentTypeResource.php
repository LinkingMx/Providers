<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // Spanish labels for the resource
    protected static ?string $navigationLabel = 'Tipos de Documento';

    protected static ?string $modelLabel = 'Tipo de Documento';

    protected static ?string $pluralModelLabel = 'Tipos de Documento';

    // Navigation group
    protected static ?string $navigationGroup = 'Configuración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255)
                    ->unique(DocumentType::class, 'name', ignoreRecord: true)
                    ->helperText('Nombre único del tipo de documento (ej. "Pasaporte", "Licencia de Conducir")'),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3)
                    ->maxLength(1000)
                    ->helperText('Descripción detallada del tipo de documento y sus requisitos'),

                Forms\Components\CheckboxList::make('allowed_file_types')
                    ->label('Tipos de archivo permitidos')
                    ->required()
                    ->options([
                        'pdf' => 'PDF - Documentos portátiles',
                        'jpg' => 'JPG - Imágenes JPEG',
                        'jpeg' => 'JPEG - Imágenes JPEG',
                        'png' => 'PNG - Imágenes PNG',
                        'gif' => 'GIF - Imágenes GIF',
                        'doc' => 'DOC - Documentos Word',
                        'docx' => 'DOCX - Documentos Word',
                        'xml' => 'XML - Documentos XML',
                        'txt' => 'TXT - Archivos de texto',
                    ])
                    ->columns(3)
                    ->helperText('Selecciona los formatos de archivo que se permiten para este tipo de documento'),

                Forms\Components\TextInput::make('validity_days')
                    ->label('Días de validez')
                    ->numeric()
                    ->required()
                    ->default(365)
                    ->minValue(0)
                    ->maxValue(9999)
                    ->suffix('días')
                    ->helperText('Número de días que el documento permanece válido. Usar 0 para documentos que no expiran'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->helperText('Los tipos de documento inactivos no aparecerán en las listas de selección'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('allowed_file_types')
                    ->label('Tipos permitidos')
                    ->formatStateUsing(fn (array $state): string => implode(', ', $state))
                    ->badge()
                    ->separator(',')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('validity_days')
                    ->label('Validez')
                    ->formatStateUsing(function ($state) {
                        return $state == 0 ? 'Sin expiración' : $state.' días';
                    })
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Activo')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Tipo de documento actualizado')
                            ->body('El tipo de documento ha sido actualizado exitosamente.')
                            ->icon('heroicon-o-document-text')
                            ->duration(5000)
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Tipos de documento eliminados')
                                ->body('Los tipos de documento seleccionados han sido eliminados exitosamente.')
                                ->icon('heroicon-o-trash')
                                ->duration(5000)
                        ),
                ]),
            ]);
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
