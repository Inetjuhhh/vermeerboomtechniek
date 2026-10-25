<?php

namespace App\Filament\Resources\CalculatorServiceResource\Pages;

use App\Filament\Resources\CalculatorServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCalculatorService extends EditRecord
{
    protected static string $resource = CalculatorServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
