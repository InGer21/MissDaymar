<?php

namespace App\Filament\Resources\ProductPresentations\Pages;

use App\Filament\Resources\ProductPresentations\ProductPresentationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProductPresentation extends EditRecord
{
    protected static string $resource = ProductPresentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
