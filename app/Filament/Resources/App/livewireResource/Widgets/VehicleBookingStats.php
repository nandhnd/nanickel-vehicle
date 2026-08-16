<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VehicleBookingStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Kendaraan',
                Vehicle::count()
            )
                ->description('Seluruh kendaraan')
                ->icon('heroicon-o-truck'),

            Stat::make(
                'Kendaraan Tersedia',
                Vehicle::where(
                    'status',
                    'tersedia'
                )->count()
            )
                ->description('Siap digunakan')
                ->icon('heroicon-o-check-circle'),

            Stat::make(
                'Kendaraan Dipakai',
                Vehicle::where(
                    'status',
                    'dipakai'
                )->count()
            )
                ->description('Sedang digunakan')
                ->icon('heroicon-o-arrow-path'),

            Stat::make(
                'Maintenance',
                Vehicle::where(
                    'status',
                    'maintenance'
                )->count()
            )
                ->description('Perlu perawatan')
                ->icon('heroicon-o-wrench-screwdriver'),

            Stat::make(
                'Booking Pending',
                Booking::where(
                    'status',
                    'pending'
                )->count()
            )
                ->description('Menunggu approval')
                ->icon('heroicon-o-clock'),

            Stat::make(
                'Booking Ongoing',
                Booking::where(
                    'status',
                    'ongoing'
                )->count()
            )
                ->description('Sedang berjalan')
                ->icon('heroicon-o-play'),
        ];
    }
}