<?php

namespace App\Filament\Resources\LooseGoods\Pages;

use App\Filament\Resources\LooseGoods\LooseGoodResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLooseGood extends EditRecord
{
    protected static string $resource = LooseGoodResource::class;

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
