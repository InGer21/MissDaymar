<?php

namespace App\Filament\Resources\Conversions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ConversionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
