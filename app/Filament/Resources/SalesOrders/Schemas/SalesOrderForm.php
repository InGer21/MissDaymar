<?php

namespace App\Filament\Resources\SalesOrders\Schemas;

use App\Models\ProductPresentation;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SalesOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('entity_id')
                    ->label('Cliente')
                    ->relationship('entity', 'name')
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Notas')
                    ->placeholder('Dirección de entrega, instrucciones, etc.')
                    ->rows(2)
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label('Productos')
                    ->relationship('items')
                    ->schema([
                        Select::make('presentation_id')
                            ->label('Producto')
                            ->options(fn () => ProductPresentation::with('product')
                                ->get()
                                ->mapWithKeys(fn ($p) => [
                                    $p->id => "{$p->product->name} | {$p->presentation_type} {$p->format}",
                                ])
                                ->toArray())
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $pres = ProductPresentation::with('prices')->find($state);
                                if (! $pres) {
                                    return;
                                }
                                $price = $pres->prices->first();
                                if ($price) {
                                    $set('unit_price_usd', $price->unit_price_usd);
                                }
                            }),
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->minValue(0.001)
                            ->step(0.001)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('subtotal_usd', round(($state ?? 0) * ($get('unit_price_usd') ?? 0), 2));
                            }),
                        TextInput::make('unit_price_usd')
                            ->label('Precio ($)')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('subtotal_usd', round(($get('quantity') ?? 0) * ($state ?? 0), 2));
                            }),
                        TextInput::make('subtotal_usd')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->table([
                        TableColumn::make('Producto'),
                        TableColumn::make('Cantidad'),
                        TableColumn::make('Precio ($)'),
                        TableColumn::make('Subtotal'),
                    ])
                    ->compact()
                    ->defaultItems(0)
                    ->addActionLabel('Agregar producto')
                    ->columnSpanFull(),
                TextInput::make('total_usd')
                    ->label('Total ($)')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->readOnly()
                    ->dehydrated()
                    ->columnSpanFull(),
                Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
