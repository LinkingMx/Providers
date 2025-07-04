<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    // Spanish labels for the resource
    protected static ?string $navigationLabel = 'Usuarios / Proveedores';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    // Navigation group
    protected static ?string $navigationGroup = 'Administración';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información general')
                    ->description('Datos básicos y credenciales de acceso del usuario o proveedor.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nombre completo del usuario'),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Dirección de correo electrónico única del usuario'),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->confirmed()
                            ->minLength(8)
                            ->helperText('Mínimo 8 caracteres. Requerida solo al crear nuevos usuarios'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(false)
                            ->helperText('Debe coincidir con la contraseña ingresada arriba'),
                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->live()
                            ->helperText('Selecciona los roles que tendrá este usuario en el sistema'),
                        Forms\Components\Select::make('branches')
                            ->label('Sucursales')
                            ->relationship('branches', 'name', fn ($query) => $query->where('is_active', true))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull()
                            ->helperText('Sucursales a las que pertenece el usuario. Solo sucursales activas están disponibles.'),
                    ])
                    ->columns(2)
                    ->aside(),

                Section::make('Datos de proveedor')
                    ->description('Información fiscal y tipo de proveedor. Visible solo si el usuario tiene el rol de proveedor.')
                    ->schema([
                        Forms\Components\TextInput::make('rfc')
                            ->label('RFC')
                            ->maxLength(13)
                            ->minLength(10)
                            ->helperText('Registro Federal de Contribuyentes (RFC) del proveedor')
                            ->placeholder('Ej: XAXX010101000'),
                        Forms\Components\TextInput::make('business_name')
                            ->label('Razón social')
                            ->maxLength(255)
                            ->helperText('Nombre comercial o razón social del proveedor (opcional)'),
                        Forms\Components\Select::make('provider_type_id')
                            ->label('Tipo de Proveedor')
                            ->options(fn () => \App\Models\ProviderType::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->helperText('Selecciona el tipo de proveedor'),
                    ])
                    ->hidden(function (Get $get): bool {
                        $providerRole = Role::where('name', 'Provider')->first();
                        if (! $providerRole) {
                            return true;
                        }
                        $selectedRoles = $get('roles') ?? [];

                        return ! in_array($providerRole->id, $selectedRoles);
                    })
                    ->columns(2)
                    ->collapsible(),
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

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Admin' => 'danger',
                        'Provider' => 'success',
                        'super_admin' => 'warning',
                        'user' => 'gray',
                        default => 'primary',
                    })
                    ->separator(','),

                Tables\Columns\TextColumn::make('providerProfile.rfc')
                    ->label('RFC')
                    ->placeholder('No aplica')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('providerProfile.providerType.name')
                    ->label('Tipo de Proveedor')
                    ->badge()
                    ->color('info')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('branches.name')
                    ->label('Sucursales')
                    ->badge()
                    ->color('info')
                    ->separator(',')
                    ->placeholder('Sin asignar')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email verificado')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No verificado')
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Filtrar por rol')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('branches')
                    ->label('Filtrar por sucursal')
                    ->relationship('branches', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('verified')
                    ->label('Email verificado')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),

                Tables\Filters\Filter::make('unverified')
                    ->label('Email no verificado')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            // RelationManagers\DocumentRequirementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeFill(array $data): array
    {
        // Si existe providerProfile, extraer los campos planos
        if (isset($data['providerProfile'])) {
            $data['rfc'] = $data['providerProfile']['rfc'] ?? null;
            $data['business_name'] = $data['providerProfile']['business_name'] ?? null;
            $data['provider_type_id'] = $data['providerProfile']['provider_type_id'] ?? null;
        }

        return $data;
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // No hacer nada especial aquí, se maneja en la Page
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        // No hacer nada especial aquí, se maneja en la Page
        return $data;
    }
}
