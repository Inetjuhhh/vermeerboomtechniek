<?php

namespace App\Filament\Resources\CalculatorOtherCostResource\Pages;

use App\Filament\Resources\CalculatorOtherCostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalculatorOtherCosts extends ListRecords
{
    protected static string $resource = CalculatorOtherCostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
