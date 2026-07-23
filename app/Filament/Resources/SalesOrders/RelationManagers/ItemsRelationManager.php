<?php

namespace App\Filament\Resources\SalesOrders\RelationManagers;

use App\Models\ProductPresentation;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
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
                Select::make('presentation_id')
                    ->label('Producto')
                    ->searchable()
                    ->required()
                    ->live()
                    ->getSearchResultsUsing(fn (string $search): array => ProductPresentation::with('product')
                        ->whereIn('presentation_type', ['bulto', 'saco'])
                        ->where(function ($q) use ($search) {
                            $q->whereHas('product', fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
                                ->orWhere('format', 'ilike', "%{$search}%");
                        })
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn ($p) => [
                            $p->id => "{$p->product->name} | {$p->presentation_type} {$p->format}",
                        ])
                        ->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => ($p = ProductPresentation::with('product')->find($value))
                        ? "{$p->product->name} | {$p->presentation_type} {$p->format}"
                        : null)
                    ->afterStateUpdated(function ($state, $set) {
                        $pres = ProductPresentation::with('prices')->find($state);
                        if (! $pres) {
                            return;
                        }
                        $price = $pres->prices->first();
                        if ($price) {
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
                TextInput::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated(),
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
                    ->label('Precio Unit. ($)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subtotal_usd')
                    ->label('Subtotal ($)')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
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
