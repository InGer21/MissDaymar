<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class StockAlerts extends TableWidget
{
    protected static ?int $sort = 2;

    // Sin carga diferida: la consulta tarda ~15ms, no vale la pena el
    // recuadro de carga.
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Productos con Stock Bajo / Agotado')
            ->query(
                Product::query()
                    ->whereHas('presentations', fn ($q) => $q
                        ->whereNotIn('presentation_type', ['saco'])
                        ->where('current_stock', '<=', 0)
                    )
                    ->withSum(['presentations as total_stock' => fn ($q) => $q
                        ->whereNotIn('presentation_type', ['saco'])], 'current_stock')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Producto'),
                TextColumn::make('total_stock')
                    ->label('Stock')
                    ->numeric()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'warning'),
                TextColumn::make('line_1')
                    ->label('Línea'),
            ])
            ->paginated(false);
    }
}
