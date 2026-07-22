<?php

namespace App\Filament\Resources\ProductPresentations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductPresentationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->required(),
                Select::make('presentation_type')
                    ->label('Tipo de Presentación')
                    ->required()
                    ->options([
                        'bolsa_individual' => 'Bolsa Individual',
                        'por_kilo' => 'Por Kilo',
                        'ristra' => 'Ristra',
                        'bulto' => 'Bulto',
                        'medio_bulto' => 'Medio Bulto',
                        'saco' => 'Saco',
                        'bolsa_4kg' => 'Bolsa 4kg',
                    ]),
                TextInput::make('format')
                    ->label('Formato')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('ej: 18 gr, 1 kg, 10x18 gr'),
                Select::make('unit')
                    ->label('Unidad')
                    ->required()
                    ->options([
                        'g' => 'Gramos',
                        'kg' => 'Kilogramos',
                        'unit' => 'Unidad',
                        'sack' => 'Saco',
                        'multipack' => 'Multipack',
                    ]),
                TextInput::make('current_stock')
                    ->label('Stock Actual')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->helperText('Actualizado automáticamente por movimientos de inventario'),
                Toggle::make('is_active')
                    ->label('¿Activo?'),
                TextInput::make('price_usd')
                    ->label('Precio (USD)')
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->helperText('Gestionar precios desde el producto → Presentaciones')
                    ->afterStateHydrated(fn ($record, $set) => $set('price_usd', $record?->prices?->first()?->price_usd))
                    ->dehydrated(false),
                TextInput::make('profit_unit_code')
                    ->label('Cód. Unidad Profit')
                    ->maxLength(20),
                TextInput::make('profit_equivalence')
                    ->label('Equivalencia Profit')
                    ->numeric(),
                Toggle::make('is_main_unit')
                    ->label('¿Unidad Principal?'),
            ]);
    }
}
