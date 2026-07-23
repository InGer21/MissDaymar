<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RawMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('code')
                    ->label('Código')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Select::make('product_id')
                    ->label('Producto Asociado')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->nullable(),
                TextInput::make('purchase_presentation')
                    ->label('Presentación de Compra')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('ej: 25 kg, Saco 50kg'),
                Select::make('unit')
                    ->label('Unidad')
                    ->required()
                    ->options([
                        'g' => 'Gramos',
                        'kg' => 'Kilogramos',
                        'unit' => 'Unidad',
                        'sack' => 'Saco',
                    ]),
                TextInput::make('unit_cost')
                    ->label('Costo Unitario ($)')
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
                TextInput::make('stock')
                    ->label('Stock Actual')
                    ->numeric()
                    ->disabled()
                    ->helperText('Stock del producto asociado. Se actualiza con conversiones.'),
            ]);
    }
}
