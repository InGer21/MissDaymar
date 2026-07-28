<?php

namespace App\Filament\Resources\Entities\Tables;

use App\Models\Entity;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EntitiesTable
{
    /**
     * Compartida entre Clientes y Proveedores. La columna y el filtro de
     * "Tipo" desaparecen: ya está implícito en la sección donde se está.
     */
    public static function configure(Table $table, string $type = Entity::TYPE_CUSTOMER): Table
    {
        $isCustomer = $type === Entity::TYPE_CUSTOMER;

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rif')
                    ->label('RIF')
                    ->searchable(),
                TextColumn::make('sunagro')
                    ->label('SUNAGRO')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('fiscal_state')
                    ->label('Estado')
                    ->placeholder('—'),
                TextColumn::make('fiscal_city')
                    ->label('Ciudad')
                    ->placeholder('—'),
                TextColumn::make('vendor.name')
                    ->label('Vendedor')
                    ->placeholder('—')
                    ->visible(fn () => $isCustomer && auth()->user()?->role === 'admin'),
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
            ->defaultSort('name')
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
