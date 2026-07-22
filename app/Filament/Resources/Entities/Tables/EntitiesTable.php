<?php

namespace App\Filament\Resources\Entities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EntitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'customer' => 'success',
                        'supplier' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'customer' => 'Cliente',
                        'supplier' => 'Proveedor',
                    }),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('rif')
                    ->label('RIF')
                    ->searchable(),
                TextColumn::make('sunagro')
                    ->label('SUNAGRO')
                    ->searchable(),
                TextColumn::make('fiscal_state')
                    ->label('Estado'),
                TextColumn::make('fiscal_city')
                    ->label('Ciudad'),
                TextColumn::make('vendor.name')
                    ->label('Vendedor')
                    ->visible(fn () => auth()->user()?->role === 'admin'),
                TextColumn::make('profit_code')
                    ->label('Cód. Profit')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('¿Activo?')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'customer' => 'Cliente',
                        'supplier' => 'Proveedor',
                    ]),
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
