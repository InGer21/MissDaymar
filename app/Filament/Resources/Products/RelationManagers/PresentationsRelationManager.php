<?php

namespace App\Filament\Resources\Products\RelationManagers;

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
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PresentationsRelationManager extends RelationManager
{
    protected static string $relationship = 'presentations';

    protected static ?string $title = 'Presentaciones';

    protected static ?string $recordTitleAttribute = 'format';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Select::make('presentation_type')
                    ->label('Tipo')
                    ->required()
                    ->options([
                        'bolsa_individual' => 'Bolsa Individual',
                        'por_kilo' => 'Por Kilo',
                        'ristra' => 'Ristra',
                        'bulto' => 'Bulto',
                        'medio_bulto' => 'Medio Bulto',
                        'saco' => 'Saco',
                        'bolsa_4kg' => 'Bolsa 4kg',
                    ]),
                TextInput::make('format')
                    ->label('Formato')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('ej: 18 gr, 1 kg'),
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
                TextInput::make('price_usd')
                    ->label('Precio (USD)')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->afterStateHydrated(function ($record, $set) {
                        $price = $record?->prices?->first()?->price_usd;
                        $set('price_usd', $price);
                    })
                    ->dehydrateStateUsing(function ($state, $record) {
                        if ($record && $state !== null && $state !== '') {
                            session()->put('price_usd_'.$record->id, (float) $state);
                        }

                        return null;
                    })
                    ->dehydrated(false),
                TextInput::make('current_stock')
                    ->label('Stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('¿Activo?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('format')
            ->columns([
                TextColumn::make('presentation_type')
                    ->label('Tipo'),
                TextColumn::make('format')
                    ->label('Formato')
                    ->searchable(),
                TextColumn::make('unit')
                    ->label('Unidad'),
                TextColumn::make('price_usd')
                    ->label('Precio ($)')
                    ->money('USD')
                    ->state(fn ($record) => $record->prices->first()?->price_usd),
                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('¿Activo?')
                    ->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
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
                ->with('prices')
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
