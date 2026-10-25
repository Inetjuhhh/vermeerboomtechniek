<?php

namespace App\Filament\Resources\CalculatorServiceResource\Pages;

use App\Filament\Resources\CalculatorServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalculatorServices extends ListRecords
{
    protected static string $resource = CalculatorServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
