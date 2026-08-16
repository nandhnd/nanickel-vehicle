<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BookingExport implements
    FromCollection,
    WithHeadings
{
    public function __construct(
        protected ?string $startDate = null,
        protected ?string $endDate = null
    ) {}

    public function collection()
    {
        return Booking::query()
            ->with([
                'requesterOffice',
                'vehicle',
                'driver',
            ])
            ->when(
                $this->startDate,
                fn ($query) =>
                    $query->whereDate(
                        'start_datetime',
                        '>=',
                        $this->startDate
                    )
            )
            ->when(
                $this->endDate,
                fn ($query) =>
                    $query->whereDate(
                        'start_datetime',
                        '<=',
                        $this->endDate
                    )
            )
            ->orderBy(
                'start_datetime',
                'desc'
            )
            ->get()
            ->map(function ($booking) {
                return [
                    $booking->booking_code,
                    $booking->requester_name,
                    $booking->requesterOffice?->name,
                    $booking->vehicle?->plate_number,
                    $booking->driver?->name,
                    $booking->purpose,
                    $booking->destination,
                    $booking->start_datetime,
                    $booking->end_datetime,
                    $booking->status,
                    $booking->start_odometer,
                    $booking->end_odometer,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Booking',
            'Pemohon',
            'Kantor',
            'Kendaraan',
            'Driver',
            'Keperluan',
            'Tujuan',
            'Mulai',
            'Selesai',
            'Status',
            'Start Odometer',
            'End Odometer',
        ];
    }
}