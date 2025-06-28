<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderTypeResource\Pages;
use App\Models\ProviderType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderTypeResource extends Resource
{
    protected static ?string $model = ProviderType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Tipos de Proveedor';

    protected static ?string $pluralModelLabel = 'Tipos de Proveedor';

    protected static ?string $modelLabel = 'Tipo de Proveedor';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del tipo de proveedor')
                    ->description('Define el nombre y características del tipo de proveedor. Estos tipos permiten categorizar y asociar documentos requeridos.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->unique(table: ProviderType::class, ignorable: fn ($record) => $record)
                            ->maxLength(255)
                            ->helperText('Nombre único y representativo para el tipo de proveedor.'),
                    ])
                    ->columns(1)
                    ->aside(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Creado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProviderTypes::route('/'),
            'create' => Pages\CreateProviderType::route('/create'),
            'edit' => Pages\EditProviderType::route('/{record}/edit'),
        ];
    }
}
