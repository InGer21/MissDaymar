<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class StockAlerts extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Productos con Stock Bajo / Agotado')
            ->query(
                Product::query()
                    ->whereHas('presentations', fn ($q) => $q
                        ->where(function ($q) {
                            $q->where('presentation_type', 'bulto')
                              ->orWhere(fn ($q) => $q->where('presentation_type', 'por_kilo')->where('unit', 'unit'));
                        })
                        ->where('current_stock', '<=', 0)
                    )
                    ->withSum(['presentations as total_stock' => fn ($q) => $q
                        ->where(function ($q) {
                            $q->where('presentation_type', 'bulto')
                              ->orWhere(fn ($q) => $q->where('presentation_type', 'por_kilo')->where('unit', 'unit'));
                        })], 'current_stock')
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
