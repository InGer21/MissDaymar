<?php

namespace App\Filament\Resources\ProductPresentations\Schemas;

use App\Models\ProductPresentation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductPresentationForm
{
    /**
     * Compartido por Sacos, Bultos y Mercancía Suelta. `$types` limita las
     * opciones de "Tipo de Presentación" a las del grupo, para que un registro
     * no pueda crearse fuera de la sección donde se está.
     *
     * @param  list<string>|null  $types
     */
    public static function configure(Schema $schema, ?array $types = null): Schema
    {
        $options = $types === null
            ? ProductPresentation::TYPE_LABELS
            : array_intersect_key(ProductPresentation::TYPE_LABELS, array_flip($types));

        return $schema
            ->columns(2)
            ->components([
                Select::make('product_id')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('presentation_type')
                    ->label('Tipo de Presentación')
                    ->required()
                    ->options($options)
                    ->default(count($options) === 1 ? array_key_first($options) : null),
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
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Se actualiza automáticamente con los movimientos de inventario'),
                Toggle::make('is_active')
                    ->label('¿Activo?')
                    ->default(true),
            ]);
    }
}
