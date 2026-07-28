<?php

namespace App\Filament\Resources\SalesOrders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesOrdersTable
{
    /**
     * Compartida por Pedidos Generados, Despachados y Despachados por Cobrar.
     * `$showDispatch` y `$showPayment` activan las columnas que solo tienen
     * sentido una vez que el pedido salió del almacén.
     */
    public static function configure(Table $table, bool $showDispatch = false, bool $showPayment = false): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('entity.name')
                    ->label('Cliente')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Creado por')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'under_review' => 'warning',
                        'invoicing' => 'info',
                        'invoiced' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'under_review' => 'En Revisión',
                        'invoicing' => 'En Facturación',
                        'invoiced' => 'Facturado',
                        'cancelled' => 'Cancelado',
                    }),
                TextColumn::make('total_usd')
                    ->label('Total ($)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dispatched_at')
                    ->label('Despachado')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->visible($showDispatch)
                    ->sortable(),
                TextColumn::make('invoice.paid_at')
                    ->label('Cobro')
                    ->badge()
                    ->placeholder('Por cobrar')
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => $state ? 'Cobrada '.$state->format('d/m/Y') : 'Por cobrar')
                    ->visible($showPayment),
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pendiente',
                        'under_review' => 'En Revisión',
                        'invoicing' => 'En Facturación',
                        'invoiced' => 'Facturado',
                        'cancelled' => 'Cancelado',
                    ]),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Desde'),
                        DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d))),
                Filter::make('mine')
                    ->label('Mis Órdenes')
                    ->query(fn (Builder $query) => $query->where('user_id', auth()->id()))
                    ->visible(fn () => auth()->user()?->role === 'vendedor'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
