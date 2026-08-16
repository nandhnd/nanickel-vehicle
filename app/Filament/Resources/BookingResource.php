<?php

namespace App\Filament\Resources\BookingResource;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use App\Services\ActivityLogService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Bookings';

    protected static ?string $navigationGroup = 'Operational';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pemohon')
                    ->description(
                        'Pemohon adalah pihak yang mengajukan kebutuhan kendaraan.'
                    )
                    ->schema([
                        Forms\Components\TextInput::make('requester_name')
                            ->label('Nama Pemohon')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('requester_office_id')
                            ->label('Office Pemohon')
                            ->relationship(
                                name: 'requesterOffice',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                    Forms\Components\Section::make('Approval')
                    ->description(
                        'Tentukan approver yang akan memproses booking ini.'
                    )
                    ->schema([
                        Forms\Components\Select::make(
                            'approver_level_1_id'
                        )
                            ->label('Approver Level 1')
                            ->options(
                                fn () => User::query()
                                    ->where('role', 'approver')
                                    ->where('approval_level', 1)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make(
                            'approver_level_2_id'
                        )
                            ->label('Approver Level 2')
                            ->options(
                                fn () => User::query()
                                    ->where('role', 'approver')
                                    ->where('approval_level', 2)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                    Forms\Components\Section::make('Kebutuhan Perjalanan')
                    ->schema([
                        Forms\Components\Select::make('vehicle_id')
                            ->label('Kendaraan')
                            ->relationship(
                                name: 'vehicle',
                                titleAttribute: 'plate_number'
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(
                                fn () => \App\Models\Vehicle::query()
                                    ->where('status', 'tersedia')
                                    ->orderBy('plate_number')
                                    ->pluck(
                                        'plate_number',
                                        'id'
                                    )
                            ),

                        Forms\Components\Select::make('driver_id')
                            ->label('Driver')
                            ->relationship(
                                name: 'driver',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->options(
                                fn () => \App\Models\Driver::query()
                                    ->where('status', 'tersedia')
                                    ->orderBy('name')
                                    ->pluck(
                                        'name',
                                        'id'
                                    )
                            ),

                        Forms\Components\Textarea::make('purpose')
                            ->label('Keperluan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('destination')
                            ->label('Tujuan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jadwal')
                    ->schema([
                        Forms\Components\DateTimePicker::make(
                            'start_datetime'
                        )
                            ->label('Mulai')
                            ->required()
                            ->seconds(false)
                            ->native(false),

                        Forms\Components\DateTimePicker::make(
                            'end_datetime'
                        )
                            ->label('Selesai')
                            ->required()
                            ->seconds(false)
                            ->native(false)
                            ->after('start_datetime'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->default('pending')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('current_level')
                            ->label('Approval Level')
                            ->default(1)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('requester_name')
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'requesterOffice.name'
                )
                    ->label('Office Pemohon')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'vehicle.plate_number'
                )
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'driver.name'
                )
                    ->label('Driver')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('destination')
                    ->label('Tujuan')
                    ->limit(30)
                    ->tooltip(
                        fn (Booking $record): string =>
                            $record->destination
                    ),

                Tables\Columns\TextColumn::make(
                    'start_datetime'
                )
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'end_datetime'
                )
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'approverLevelOne.name'
                )
                    ->label('Approver Level 1')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make(
                    'approverLevelTwo.name'
                )
                    ->label('Approver Level 2')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'ongoing' => 'Ongoing',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                            default => $state,
                        }
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_level')
                    ->label('Level')
                    ->badge(),

                Tables\Columns\TextColumn::make(
                    'creator.name'
                )
                    ->label('Dibuat Oleh')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('start')
                    ->label('Mulai')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(function ($record): bool {
                        $user = Filament::auth()->user();

                        return $user instanceof User
                            && $user->isAdmin()
                            && $record->status === 'approved';
                    })
                    ->form([
                        Forms\Components\TextInput::make('start_odometer')
                            ->label('Start Odometer')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                    ])
                    ->action(function ($record, array $data): void {
                        $user = Filament::auth()->user();

                        if (!$record->vehicle) {
                            throw new \Exception(
                                'Kendaraan booking tidak ditemukan.'
                            );
                        }

                        if ($record->vehicle->status !== 'tersedia') {
                            throw new \Exception(
                                'Kendaraan tidak tersedia untuk digunakan.'
                            );
                        }

                        $record->update([
                            'start_odometer' => $data['start_odometer'],
                            'status' => 'ongoing',
                        ]);

                        $record->vehicle->update([
                            'status' => 'dipakai',
                        ]);

                        app(\App\Services\ActivityLogService::class)->log(
                            $user,
                            'Booking',
                            'started',
                            'Booking ' .
                                $record->booking_code .
                                ' dimulai dengan odometer ' .
                                $data['start_odometer'] .
                                '.'
                        );
                    }),
                Tables\Actions\Action::make('complete')
                ->label('Selesaikan')
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->visible(function ($record): bool {
                    $user = Filament::auth()->user();

                    return $user instanceof User
                        && $user->isAdmin()
                        && $record->status === 'ongoing';
                })
                ->form([
                    Forms\Components\TextInput::make('end_odometer')
                        ->label('End Odometer')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->rule(function ($record) {
                            return function (
                                string $attribute,
                                $value,
                                \Closure $fail
                            ) use ($record) {
                                if (
                                    $value < $record->start_odometer
                                ) {
                                    $fail(
                                        'End odometer tidak boleh lebih kecil dari start odometer.'
                                    );
                                }
                            };
                        }),
                ])
                ->action(function ($record, array $data): void {
                    $user = Filament::auth()->user();

                    $record->update([
                        'end_odometer' => $data['end_odometer'],
                        'status' => 'completed',
                    ]);

                    if ($record->vehicle) {
                        $record->vehicle->update([
                            'status' => 'tersedia',
                            'last_odometer' => $data['end_odometer'],
                        ]);
                    }

                    app(\App\Services\ActivityLogService::class)->log(
                        $user,
                        'Booking',
                        'completed',
                        'Booking ' .
                            $record->booking_code .
                            ' selesai dengan odometer ' .
                            $data['end_odometer'] .
                            '.'
                    );
                }),
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn (Booking $record): bool =>
                            $record->status === 'pending'
                    ),

                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function ($record): bool {
                        $user = Filament::auth()->user();

                        return $user instanceof User
                            && $user->isAdmin()
                            && in_array(
                                $record->status,
                                [
                                    'pending',
                                    'approved',
                                    'ongoing',
                                ],
                                true
                            );
                    })
                    ->action(function ($record): void {
                        $user = Filament::auth()->user();

                        $record->update([
                            'status' => 'cancelled',
                        ]);

                        app(ActivityLogService::class)->log(
                            $user,
                            'Booking',
                            'cancelled',
                            'Booking ' .
                                $record->booking_code .
                                ' dibatalkan oleh admin.'
                        );
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make(
                    'requester_office_id'
                )
                    ->label('Office Pemohon')
                    ->relationship(
                        'requesterOffice',
                        'name'
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort(
                'created_at',
                'desc'
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}