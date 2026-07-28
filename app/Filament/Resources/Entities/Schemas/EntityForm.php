<?php

namespace App\Filament\Resources\Entities\Schemas;

use App\Models\Entity;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EntityForm
{
    /**
     * Compartido entre Clientes y Proveedores. El `type` no se pide al
     * usuario: lo fija el Resource desde el que se crea el registro.
     */
    public static function configure(Schema $schema, string $type = Entity::TYPE_CUSTOMER): Schema
    {
        $isCustomer = $type === Entity::TYPE_CUSTOMER;

        return $schema
            ->columns(2)
            ->components([
                Hidden::make('type')
                    ->default($type),
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
                    ->label('¿Activo?')
                    ->default(true),
                // El vendedor asignado solo aplica a clientes.
                Select::make('user_id')
                    ->label('Vendedor')
                    ->relationship('vendor', 'name', fn ($q) => $q->where('role', 'vendedor')->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->visible(fn () => $isCustomer && auth()->user()?->role === 'admin'),
            ]);
    }
}
