<?php

namespace App\Filament\Resources\RentalCompanyResource\Pages;

use App\Filament\Resources\RentalCompanyResource\RentalCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalCompanies extends ListRecords
{
    protected static string $resource = RentalCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Perusahaan Rental'),
        ];
    }
}