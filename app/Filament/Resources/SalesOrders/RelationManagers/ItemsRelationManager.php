<?php

namespace App\Filament\Resources\SalesOrders\RelationManagers;

use App\Models\Product;
use App\Models\ProductPresentation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Items de la Orden';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('_product_sku')
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
                    }),
                Select::make('presentation_id')
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

                                return [$p->id => "{$p->presentation_type} {$p->format}{$p->unit} — \$".($price ?? '0.00')];
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
                    }),
                TextInput::make('quantity')
                    ->label('Cantidad')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('subtotal_usd', round(($state ?? 0) * ($get('unit_price_usd') ?? 0), 2));
                    }),
                TextInput::make('unit_price_usd')
                    ->label('Precio ($)')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('$')
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('subtotal_usd', round(($get('quantity') ?? 0) * ($state ?? 0), 2));
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('presentation.product.name')
                    ->label('Producto'),
                TextColumn::make('presentation.format')
                    ->label('Presentación'),
                TextColumn::make('quantity')
                    ->label('Cant.')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price_usd')
                    ->label('Precio ($)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('presentation.product')
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
