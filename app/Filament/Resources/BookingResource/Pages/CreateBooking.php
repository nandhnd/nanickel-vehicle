<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource\BookingResource;
use App\Services\BookingService;
use App\Services\ActivityLogService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function handleRecordCreation(
        array $data
    ): \Illuminate\Database\Eloquent\Model {
        $user = Filament::auth()->user();

        $booking = app(BookingService::class)
            ->create(
                $data,
                $user
            );

        Notification::make()
            ->title('Booking berhasil dibuat')
            ->body(
                "Kode booking: {$booking->booking_code}"
            )
            ->success()
            ->send();

        return $booking;
    }

    protected function afterCreate(): void
    {
        $booking = $this->record;

        $user = Filament::auth()->user();

        if (!$user) {
            return;
        }

        app(ActivityLogService::class)->log(
            $user,
            'Booking',
            'created',
            'Booking ' .
                $booking->booking_code .
                ' dibuat.'
        );
    }
}