<?php

namespace App\Filament\Resources\ProductPresentations\Pages;

use App\Filament\Resources\ProductPresentations\ProductPresentationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductPresentation extends CreateRecord
{
    protected static string $resource = ProductPresentationResource::class;
}
