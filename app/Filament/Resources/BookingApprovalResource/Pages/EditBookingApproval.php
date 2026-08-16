<?php

namespace App\Filament\Resources\BookingApprovalResource\Pages;

use App\Filament\Resources\BookingApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookingApproval extends EditRecord
{
    protected static string $resource = BookingApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
