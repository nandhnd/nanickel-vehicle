<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use App\Filament\Resources\RegionResource\RegionResource;
use App\Filament\Resources\OfficeResource\OfficeResource;
use App\Filament\Resources\UserResource\UserResource;
use App\Filament\Resources\VehicleTypeResource\VehicleTypeResource;
use App\Filament\Resources\RentalCompanyResource\RentalCompanyResource;
use App\Filament\Resources\VehicleResource\VehicleResource;
use App\Filament\Resources\DriverResource\DriverResource;
use App\Filament\Resources\BookingResource\BookingResource;
use App\Filament\Resources\BookingApprovalResource\BookingApprovalResource;
use App\Filament\Resources\FuelLogResource\FuelLogResource;
use App\Filament\Resources\ServiceScheduleResource\ServiceScheduleResource;
use App\Filament\Resources\ActivityLogResource\ActivityLogResource;
use App\Filament\Widgets\VehicleBookingStats;
use App\Filament\Widgets\VehicleUsageChart;
use App\Filament\Pages\BookingReport;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])

            ->resources([
                RegionResource::class,
                OfficeResource::class,
                UserResource::class,
                VehicleTypeResource::class,
                RentalCompanyResource::class,
                VehicleResource::class,
                DriverResource::class,
                BookingResource::class,
                BookingApprovalResource::class,
                FuelLogResource::class,
                ServiceScheduleResource::class,
                ActivityLogResource::class,
            ])


            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )

            ->pages([
                Pages\Dashboard::class,
                BookingReport::class,
            ])

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )

            ->widgets([
                VehicleUsageChart::class,
                VehicleBookingStats::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}