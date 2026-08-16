<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VehicleUsageChart extends ChartWidget
{
    protected static ?string $heading =
        'Grafik Pemakaian Kendaraan';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()
                ->subMonths($i);

            $labels[] = $date->translatedFormat('M');

            $data[] = Booking::query()
                ->whereIn('status', [
                    'approved',
                    'ongoing',
                    'completed',
                ])
                ->whereYear(
                    'start_datetime',
                    $date->year
                )
                ->whereMonth(
                    'start_datetime',
                    $date->month
                )
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemakaian Kendaraan',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}