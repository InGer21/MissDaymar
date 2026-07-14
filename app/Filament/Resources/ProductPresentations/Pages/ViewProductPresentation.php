<?php

namespace App\Filament\Resources\ProductPresentations\Pages;

use App\Filament\Resources\ProductPresentations\ProductPresentationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductPresentation extends ViewRecord
{
    protected static string $resource = ProductPresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
