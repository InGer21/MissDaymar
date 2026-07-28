<?php

namespace App\Filament\Resources\LooseGoods\Pages;

use App\Filament\Resources\LooseGoods\LooseGoodResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLooseGood extends ViewRecord
{
    protected static string $resource = LooseGoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
