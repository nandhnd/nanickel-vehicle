<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\BookingExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Facades\Filament;
use App\Models\User;

class BookingReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon =
        'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel =
        'Laporan Booking';

    protected static ?string $title =
        'Laporan Pemesanan Kendaraan';

    protected static ?string $navigationGroup =
        'Laporan';

    protected static ?int $navigationSort = 1;

    protected static string $view =
        'filament.resources.admin-resource.pages.booking-report';

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->isAdmin();
    }

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(): void
    {
        $this->startDate = now()
            ->startOfMonth()
            ->format('Y-m-d');

        $this->endDate = now()
            ->endOfMonth()
            ->format('Y-m-d');
    }

    public function table(
        Table $table
    ): Table {
        return $table
            ->query(
                Booking::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make(
                    'booking_code'
                )
                    ->label('Kode Booking')
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'requester_name'
                )
                    ->label('Pemohon'),

                Tables\Columns\TextColumn::make(
                    'requesterOffice.name'
                )
                    ->label('Kantor'),

                Tables\Columns\TextColumn::make(
                    'vehicle.plate_number'
                )
                    ->label('Kendaraan'),

                Tables\Columns\TextColumn::make(
                    'driver.name'
                )
                    ->label('Driver'),

                Tables\Columns\TextColumn::make(
                    'purpose'
                )
                    ->label('Keperluan')
                    ->limit(40),

                Tables\Columns\TextColumn::make(
                    'destination'
                )
                    ->label('Tujuan')
                    ->limit(40),

                Tables\Columns\TextColumn::make(
                    'start_datetime'
                )
                    ->label('Mulai')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make(
                    'end_datetime'
                )
                    ->label('Selesai')
                    ->dateTime('d/m/Y H:i'),

                Tables\Columns\TextColumn::make(
                    'status'
                )
                    ->label('Status')
                    ->badge(),

                Tables\Columns\TextColumn::make(
                    'start_odometer'
                )
                    ->label('Start KM'),

                Tables\Columns\TextColumn::make(
                    'end_odometer'
                )
                    ->label('End KM'),
            ])
            ->filters([
                Tables\Filters\Filter::make('periode')
                    ->form([
                        Forms\Components\DatePicker::make(
                            'start'
                        )
                            ->label('Tanggal Mulai'),

                        Forms\Components\DatePicker::make(
                            'end'
                        )
                            ->label('Tanggal Selesai'),
                    ])
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {
                            return $query
                                ->when(
                                    $data['start'] ?? null,
                                    fn (
                                        Builder $query,
                                        $date
                                    ) =>
                                        $query->whereDate(
                                            'start_datetime',
                                            '>=',
                                            $date
                                        )
                                )
                                ->when(
                                    $data['end'] ?? null,
                                    fn (
                                        Builder $query,
                                        $date
                                    ) =>
                                        $query->whereDate(
                                            'start_datetime',
                                            '<=',
                                            $date
                                        )
                                );
                        }
                    ),
            ])
            ->defaultSort(
                'start_datetime',
                'desc'
            );
    }

    public function exportExcel()
    {
        return Excel::download(
            new BookingExport(
                $this->startDate,
                $this->endDate
            ),
            'laporan-booking-' .
            now()->format('Y-m-d-His') .
            '.xlsx'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportExcel'),
        ];
    }
}