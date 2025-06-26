<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentStatusResource\Pages;
use App\Models\DocumentStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentStatusResource extends Resource
{
    protected static ?string $model = DocumentStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    // Spanish labels for the resource
    protected static ?string $navigationLabel = 'Estados de Documento';

    protected static ?string $modelLabel = 'Estado de Documento';

    protected static ?string $pluralModelLabel = 'Estados de Documento';

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
                    ->unique(DocumentStatus::class, 'name', ignoreRecord: true),

                Forms\Components\Select::make('color')
                    ->label('Color')
                    ->required()
                    ->options([
                        'primary' => 'Primario',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'danger' => 'Peligro',
                        'info' => 'Información',
                    ]),

                Forms\Components\TextInput::make('icon')
                    ->label('Icono')
                    ->maxLength(255)
                    ->hint('Utiliza Heroicons (ej., "heroicon-o-clock")'),

                Forms\Components\Toggle::make('is_default')
                    ->label('Estado por defecto')
                    ->helperText('Marca este como el estado inicial para todos los documentos nuevos'),

                Forms\Components\Toggle::make('is_complete')
                    ->label('Estado de finalización')
                    ->helperText('Marca este estado como un estado final exitoso'),

                Forms\Components\Select::make('next_statuses')
                    ->label('Estados siguientes')
                    ->multiple()
                    ->relationship('next_statuses', 'name')
                    ->preload()
                    ->helperText('Selecciona a qué estados puede transicionar este estado'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($record) => $record->color)
                    ->icon(fn ($record) => $record->icon),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Por defecto')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_complete')
                    ->label('Completado')
                    ->boolean()
                    ->sortable(),

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(false), // Hide delete action to prevent deletion
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(false), // Hide bulk delete action to prevent deletion
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
            'index' => Pages\ListDocumentStatuses::route('/'),
            'create' => Pages\CreateDocumentStatus::route('/create'),
            'edit' => Pages\EditDocumentStatus::route('/{record}/edit'),
        ];
    }
}
