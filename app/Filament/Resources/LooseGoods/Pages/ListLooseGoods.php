<?php

namespace App\Filament\Resources\LooseGoods\Pages;

use App\Filament\Resources\LooseGoods\LooseGoodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLooseGoods extends ListRecords
{
    protected static string $resource = LooseGoodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
