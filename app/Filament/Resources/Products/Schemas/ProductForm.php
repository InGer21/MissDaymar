<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
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
                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name'),
                Select::make('type')
                    ->label('Tipo')
                    ->required()
                    ->options([
                        'MP' => 'Materia Prima',
                        'PT' => 'Producto Terminado',
                        'Ambos' => 'Ambos',
                        'Puro' => 'Producto Puro',
                    ]),
                TextInput::make('line_1')
                    ->label('Línea 1')
                    ->maxLength(50),
                TextInput::make('line_2')
                    ->label('Línea 2')
                    ->maxLength(50),
                TextInput::make('profit_code')
                    ->label('Código Profit')
                    ->maxLength(30),
                TextInput::make('profit_line')
                    ->label('Línea Profit')
                    ->maxLength(50),
                TextInput::make('profit_subl')
                    ->label('Sublínea Profit')
                    ->maxLength(50),
                Toggle::make('is_service')
                    ->label('¿Es Servicio?'),
            ]);
    }
}
