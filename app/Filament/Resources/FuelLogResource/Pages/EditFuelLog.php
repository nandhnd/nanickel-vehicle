<?php

namespace App\Filament\Resources\FuelLogResource\Pages;

use App\Filament\Resources\FuelLogResource\FuelLogResource;
use Filament\Resources\Pages\EditRecord;

class EditFuelLog extends EditRecord
{
    protected static string $resource =
        FuelLogResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        unset($data['recorded_by']);

        return $data;
    }
}