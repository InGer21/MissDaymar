<?php

namespace App\Filament\Resources\RawMaterials\Schemas;

use App\Models\RawMaterial;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RawMaterialForm
{
    /**
     * Compartido entre Granos y Consumibles. El `type` no se pide al usuario:
     * lo fija el Resource desde el que se crea el registro.
     */
    public static function configure(Schema $schema, string $type = RawMaterial::TYPE_GRAIN): Schema
    {
        $isGrain = $type === RawMaterial::TYPE_GRAIN;

        return $schema
            ->columns(2)
            ->components([
                Hidden::make('type')
                    ->default($type),
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
                    ->preload()
                    ->nullable()
                    ->visible($isGrain),
                TextInput::make('purchase_presentation')
                    ->label('Presentación de Compra')
                    ->required()
                    ->maxLength(100)
                    ->placeholder($isGrain ? 'ej: 25 kg, Saco 50kg' : 'ej: Bobina 50 cm, Caja x 100'),
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
                    ->minValue(0)
                    ->prefix('$'),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
                TextInput::make('stock')
                    ->label('Stock Actual')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false)
                    ->visible($isGrain)
                    ->helperText('Stock del producto asociado. Se actualiza con conversiones.'),
            ]);
    }
}
