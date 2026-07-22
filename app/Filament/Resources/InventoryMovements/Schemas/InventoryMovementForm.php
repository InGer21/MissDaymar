<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('presentation_id')
                    ->label('Presentación')
                    ->relationship('presentation', 'format')
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->label('Tipo')
                    ->required()
                    ->options([
                        'entry' => 'Entrada',
                        'exit' => 'Salida',
                        'adjustment' => 'Ajuste',
                    ]),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric(),
                TextInput::make('referenceable_type')
                    ->label('Tipo de Referencia')
                    ->hidden()
                    ->maxLength(255),
                TextInput::make('referenceable_id')
                    ->label('ID de Referencia')
                    ->hidden()
                    ->numeric(),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
