<?php

namespace App\Filament\Resources\ServiceScheduleResource\Pages;

use App\Filament\Resources\ServiceScheduleResource\ServiceScheduleResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceSchedule extends CreateRecord
{
    protected static string $resource =
        ServiceScheduleResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        $data['created_by'] = Filament::auth()->id();

        return $data;
    }
}