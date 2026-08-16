<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource\BookingResource;
use App\Services\ActivityLogService;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource =
        BookingResource::class;

    protected function afterSave(): void
    {
        $booking = $this->record;

        $user = Filament::auth()->user();

        if (!$user) {
            return;
        }

        app(ActivityLogService::class)->log(
            $user,
            'Booking',
            'updated',
            'Booking ' .
                $booking->booking_code .
                ' diperbarui.'
        );
    }
}