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
     * Compartido entre Sacos y Materiales Consumibles. El `type` no se pide al
     * usuario: lo fija el Resource desde el que se crea el registro.
     */
    public static function configure(Schema $schema, string $type = RawMaterial::TYPE_GRAIN): Schema
    {
        $isGrain = $type === RawMaterial::TYPE_GRAIN;

        return $schema
            ->columns(2)
            ->components([
                Hidden::make('type')
                    ->default($type),
                TextInput::make('sku')
                    ->label('SKU')
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->placeholder($isGrain ? 'ej: G00311' : 'ej: C00110'),
                TextInput::make('code')
                    ->label('Código interno')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true)
                    ->helperText('Código heredado del sistema anterior'),
                TextInput::make('name')
                    ->label($isGrain ? 'Nombre del grano' : 'Nombre del paquete')
                    ->required()
                    ->maxLength(255),
                TextInput::make('purchase_presentation')
                    ->label($isGrain ? 'Presentación de compra' : 'Capacidad del paquete')
                    ->required()
                    ->maxLength(100)
                    ->placeholder($isGrain ? 'ej: Saco 25 kg' : 'ej: 500 gr por paquete'),
                TextInput::make('current_stock')
                    ->label($isGrain ? 'Sacos en existencia' : 'Bobinas en existencia')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('kg_per_unit')
                    ->label('KG de grano por saco')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.001)
                    ->suffix('kg')
                    ->visible($isGrain)
                    ->helperText('Necesario para calcular los KG totales en existencia'),
                Select::make('unit')
                    ->label('Unidad')
                    ->required()
                    ->default($isGrain ? 'sack' : 'unit')
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
            ]);
    }
}
