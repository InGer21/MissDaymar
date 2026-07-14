<?php

namespace App\Filament\Resources\ProductPresentations\Pages;

use App\Filament\Resources\ProductPresentations\ProductPresentationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductPresentations extends ListRecords
{
    protected static string $resource = ProductPresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
