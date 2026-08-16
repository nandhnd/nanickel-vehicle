<?php

namespace App\Filament\Resources\FuelLogResource\Pages;

use App\Filament\Resources\FuelLogResource\FuelLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFuelLog extends CreateRecord
{
    protected static string $resource =
        FuelLogResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        $data['recorded_by'] =
            \Filament\Facades\Filament::auth()
                ->id();

        return $data;
    }
}