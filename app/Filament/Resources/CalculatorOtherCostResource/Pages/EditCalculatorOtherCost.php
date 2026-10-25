<?php

namespace App\Filament\Resources\CalculatorOtherCostResource\Pages;

use App\Filament\Resources\CalculatorOtherCostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCalculatorOtherCost extends EditRecord
{
    protected static string $resource = CalculatorOtherCostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
