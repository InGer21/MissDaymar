<?php

namespace App\Filament\Resources\Conversions\Pages;

use App\Filament\Resources\Conversions\ConversionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConversions extends ListRecords
{
    protected static string $resource = ConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
