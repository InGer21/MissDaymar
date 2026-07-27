<?php

namespace App\Filament\Resources\SalesOrders\Schemas;

use App\Models\Entity;
use App\Models\Product;
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
                static::entitySelect(),
                static::notesField(),
                static::itemsRepeater(),
                static::totalField(),
                static::hiddenFields(),
            ]);
    }

    private static function entitySelect(): Select
    {
        return Select::make('entity_id')
            ->label('Cliente')
            ->searchable()
            ->required()
            ->getSearchResultsUsing(fn (string $search): array => Entity::where('name', 'ilike', "%{$search}%")
                ->limit(50)
                ->pluck('name', 'id')
                ->toArray())
            ->getOptionLabelUsing(fn ($value): ?string => Entity::find($value)?->name)
            ->columnSpanFull();
    }

    private static function notesField(): Textarea
    {
        return Textarea::make('notes')
            ->label('Notas')
            ->placeholder('Dirección de entrega, instrucciones...')
            ->rows(2)
            ->columnSpanFull();
    }

    private static function itemsRepeater(): Repeater
    {
        return Repeater::make('items')
            ->label('Productos')
            ->relationship('items')
            ->schema([
                static::productSkuSelect(),
                static::presentationSelect(),
                static::quantityField(),
                static::priceField(),
            ])
            ->table([
                TableColumn::make('Producto'),
                TableColumn::make('Cantidad'),
                TableColumn::make('Precio ($)'),
            ])
            ->compact()
            ->defaultItems(0)
            ->addActionLabel('Agregar producto')
            ->columnSpanFull();
    }

    private static function productSkuSelect(): Select
    {
        return Select::make('_product_sku')
            ->label('Producto')
            ->searchable()
            ->required()
            ->live()
            ->getSearchResultsUsing(fn (string $search): array => Product::where('name', 'ilike', "%{$search}%")
                ->orWhere('sku', 'ilike', "%{$search}%")
                ->limit(30)
                ->get()
                ->mapWithKeys(fn ($p) => [$p->sku => ($p->sku ? "[{$p->sku}] " : '').$p->name])
                ->toArray())
            ->getOptionLabelUsing(fn ($value): ?string => ($p = Product::where('sku', $value)->first())
                ? ($p->sku ? "[{$p->sku}] " : '').$p->name
                : $value)
            ->afterStateUpdated(function ($state, $set) {
                $set('presentation_id', null);
                $set('unit_price_usd', null);
            })
            ->dehydrated(false);
    }

    private static function presentationSelect(): Select
    {
        return Select::make('presentation_id')
            ->label('Presentación')
            ->required()
            ->live()
            ->options(function ($get) {
                $sku = $get('_product_sku');

                if (! $sku) {
                    return [];
                }

                return ProductPresentation::with('product', 'prices')
                    ->whereHas('product', fn ($q) => $q->where('sku', $sku))
                    ->whereNotIn('presentation_type', ['saco'])
                    ->get()
                    ->mapWithKeys(function ($p) {
                        $price = $p->prices->first()?->price_usd;
                        $stock = $p->current_stock;
                        $stockInfo = $stock > 0 ? " | Stock: {$stock}" : ($stock < 0 ? ' | Stock: agotado' : '');

                        return [$p->id => "{$p->presentation_type} {$p->format}{$p->unit} — \$".($price ?? '0.00').$stockInfo];
                    })
                    ->toArray();
            })
            ->afterStateUpdated(function ($state, $set) {
                if (! $state) {
                    return;
                }
                $pres = ProductPresentation::with('prices')->find($state);
                if ($pres && $price = $pres->prices->first()) {
                    $set('unit_price_usd', $price->price_usd);
                }
            });
    }

    private static function quantityField(): TextInput
    {
        return TextInput::make('quantity')
            ->label('Cantidad')
            ->numeric()
            ->minValue(0.01)
            ->step(0.01)
            ->required()
            ->live()
            ->afterStateUpdated(function ($state, $set, $get) {
                $set('subtotal_usd', round(($state ?? 0) * ($get('unit_price_usd') ?? 0), 2));
            });
    }

    private static function priceField(): TextInput
    {
        return TextInput::make('unit_price_usd')
            ->label('Precio ($)')
            ->numeric()
            ->prefix('$')
            ->minValue(0)
            ->step(0.01)
            ->required()
            ->live()
            ->afterStateUpdated(function ($state, $set, $get) {
                $set('subtotal_usd', round(($get('quantity') ?? 0) * ($state ?? 0), 2));
            });
    }

    private static function totalField(): TextInput
    {
        return TextInput::make('total_usd')
            ->label('Total ($)')
            ->numeric()
            ->prefix('$')
            ->default(0)
            ->readOnly()
            ->helperText('Calculado automáticamente al guardar')
            ->columnSpanFull();
    }

    private static function hiddenFields(): array
    {
        return [
            Hidden::make('user_id')
                ->default(fn () => auth()->id()),
            TextInput::make('profit_doc_num')
                ->label('N° Documento Profit')
                ->maxLength(50),
        ];
    }
}
