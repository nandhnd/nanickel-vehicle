<?php

namespace App\Filament\Resources\BookingApprovalResource\Pages;

use App\Filament\Resources\BookingApprovalResource\BookingApprovalResource;
use Filament\Resources\Pages\ListRecords;

class ListBookingApprovals extends ListRecords
{
    protected static string $resource =
        BookingApprovalResource::class;
}