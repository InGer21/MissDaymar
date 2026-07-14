<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('N° Factura')
                    ->searchable(),
                TextColumn::make('salesOrder.entity.name')
                    ->label('Cliente'),
                TextColumn::make('salesOrder.id')
                    ->label('Orden #'),
                TextColumn::make('bcv_rate')
                    ->label('Tasa BCV')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_usd')
                    ->label('Total ($)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->label('Emitida')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
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
