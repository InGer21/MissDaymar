<?php

namespace App\Filament\Resources\Conversions\Pages;

use App\Filament\Resources\Conversions\ConversionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConversion extends ViewRecord
{
    protected static string $resource = ConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
