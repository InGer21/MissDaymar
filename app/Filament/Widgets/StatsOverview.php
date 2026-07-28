<?php

namespace App\Filament\Widgets;

use App\Models\Entity;
use App\Models\Product;
use App\Models\ProductPresentation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    // Sin carga diferida: las consultas tardan ~40ms sobre este volumen de
    // datos, así que el widget se pinta junto con la página en vez de
    // mostrar un recuadro vacío que se llena después.
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $stockCount = ProductPresentation::whereNotIn('presentation_type', ['saco'])
            ->where('current_stock', '>', 0)
            ->distinct('product_id')
            ->count('product_id');

        return [
            Stat::make('Productos', Product::count())
                ->description('En catálogo')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary'),
            Stat::make('Clientes', Entity::where('type', 'customer')->count())
                ->description('Activos')
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),
            Stat::make('Con Stock', $stockCount)
                ->description('Productos disponibles')
                ->descriptionIcon('heroicon-o-archive-box')
                ->color('warning'),
            Stat::make('Vendedores', User::where('role', 'vendedor')->where('is_active', true)->count())
                ->description('Activos')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),
        ];
    }
}
