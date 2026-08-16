<?php

namespace App\Services;

use App\Models\Booking;

class BookingCodeService
{
    public function generate(): string
    {
        $date = now()->format('Ymd');

        $lastBooking = Booking::query()
            ->whereDate('created_at', now()->toDateString())
            ->latest('id')
            ->first();

        $sequence = $lastBooking
            ? ((int) substr($lastBooking->booking_code, -4)) + 1
            : 1;

        return sprintf(
            'BK-%s-%04d',
            $date,
            $sequence
        );
    }
}