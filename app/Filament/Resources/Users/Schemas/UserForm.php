<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Dejar en blanco para mantener la contraseña actual' : null),
                Select::make('role')
                    ->label('Rol')
                    ->required()
                    ->options([
                        'admin' => 'Administrador',
                        'almacenista' => 'Almacenista',
                        'vendedor' => 'Vendedor',
                        'facturacion' => 'Facturación',
                    ])
                    ->default('vendedor'),
                Toggle::make('is_salesperson')
                    ->label('¿Es vendedor?'),
                Toggle::make('is_active')
                    ->label('¿Activo?')
                    ->default(true),
            ]);
    }
}
