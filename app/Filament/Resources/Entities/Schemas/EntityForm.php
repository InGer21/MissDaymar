<?php

namespace App\Filament\Resources\Entities\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EntityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('type')
                    ->label('Tipo')
                    ->required()
                    ->options([
                        'customer' => 'Cliente',
                        'supplier' => 'Proveedor',
                    ]),
                TextInput::make('name')
                    ->label('Nombre o Razón Social')
                    ->required()
                    ->maxLength(255),
                TextInput::make('rif')
                    ->label('RIF')
                    ->required()
                    ->maxLength(20)
                    ->placeholder('J-XXXXXXXX-X'),
                TextInput::make('sunagro')
                    ->label('SUNAGRO')
                    ->maxLength(50),
                TextInput::make('fiscal_state')
                    ->label('Estado (Domicilio Fiscal)')
                    ->required()
                    ->maxLength(100),
                TextInput::make('fiscal_city')
                    ->label('Ciudad (Domicilio Fiscal)')
                    ->required()
                    ->maxLength(100),
                Textarea::make('address')
                    ->label('Dirección')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('¿Activo?'),
                Select::make('user_id')
                    ->label('Vendedor')
                    ->relationship('vendor', 'name', fn ($q) => $q->where('is_salesperson', true)->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()?->role === 'admin'),
                TextInput::make('profit_code')
                    ->label('Código Profit')
                    ->maxLength(20)
                    ->visible(fn () => auth()->user()?->role === 'admin'),
                TextInput::make('profit_vendor')
                    ->label('Vendor Profit')
                    ->maxLength(20)
                    ->visible(fn () => auth()->user()?->role === 'admin'),
                TextInput::make('profit_zone')
                    ->label('Zona Profit')
                    ->maxLength(20)
                    ->visible(fn () => auth()->user()?->role === 'admin'),
            ]);
    }
}
