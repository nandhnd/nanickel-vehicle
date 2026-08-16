<?php

namespace App\Filament\Resources\BookingApprovalResource;

use App\Filament\Resources\BookingApprovalResource\Pages;
use App\Models\BookingApproval;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\ActivityLogService;

class BookingApprovalResource extends Resource
{
    protected static ?string $model = BookingApproval::class;

    protected static ?string $navigationIcon =
        'heroicon-o-check-badge';

    protected static ?string $navigationLabel =
        'Booking Approval';

    protected static ?string $modelLabel =
        'Booking Approval';

    protected static ?string $pluralModelLabel =
        'Booking Approval';

    protected static ?string $navigationGroup =
        'Approval';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->isApprover();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(
        \Illuminate\Database\Eloquent\Model $record
    ): bool {
        return false;
    }

    public static function canDelete(
        \Illuminate\Database\Eloquent\Model $record
    ): bool {
        return false;
    }

    public static function form(
        \Filament\Forms\Form $form
    ): \Filament\Forms\Form {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = Filament::auth()->user();

                $query
                    ->where('approver_id', $user->id)
                    ->where('status', 'menunggu');
            })

            ->columns([
                Tables\Columns\TextColumn::make(
                    'booking.booking_code'
                )
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'booking.requester_name'
                )
                    ->label('Pemohon')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'booking.requesterOffice.name'
                )
                    ->label('Office Pemohon')
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'booking.vehicle.plate_number'
                )
                    ->label('Kendaraan'),

                Tables\Columns\TextColumn::make(
                    'booking.driver.name'
                )
                    ->label('Driver'),

                Tables\Columns\TextColumn::make(
                    'booking.destination'
                )
                    ->label('Tujuan')
                    ->limit(40),

                Tables\Columns\TextColumn::make(
                    'booking.start_datetime'
                )
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'booking.end_datetime'
                )
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'level'
                )
                    ->label('Level')
                    ->badge(),

                Tables\Columns\TextColumn::make(
                    'status'
                )
                    ->label('Status')
                    ->badge(),

                Tables\Columns\TextColumn::make(
                    'created_at'
                )
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])

            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(
                        fn (
                            BookingApproval $record
                        ): string =>
                            static::getUrl(
                                'view',
                                [
                                    'record' => $record,
                                ]
                            )
                    ),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Booking?')
                    ->modalDescription(
                        'Booking akan diproses ke tahap berikutnya.'
                    )
                    ->action(function (
                        BookingApproval $record
                    ): void {
                        $user = Filament::auth()->user();

                        if (!$user instanceof User) {
                            return;
                        }

                        if ($record->approver_id !== $user->id) {
                            return;
                        }

                        if ($record->status !== 'menunggu') {
                            return;
                        }

                        $record->update([
                            'status' => 'disetujui',
                            'acted_at' => now(),
                        ]);

                        $booking = $record->booking;

                        if (!$booking) {
                            return;
                        }

                        /*
                        * Jika Level 1 disetujui,
                        * buat approval Level 2.
                        */
                        if ($record->level == 1) {
                            if ($booking->approver_level_2_id) {
                                BookingApproval::firstOrCreate(
                                    [
                                        'booking_id' => $booking->id,
                                        'level' => 2,
                                    ],
                                    [
                                        'approver_id' =>
                                            $booking->approver_level_2_id,
                                        'status' => 'menunggu',
                                    ]
                                );

                                $booking->update([
                                    'current_level' => 2,
                                ]);
                            }
                        }

                        /*
                        * Jika Level 2 disetujui,
                        * booking menjadi approved.
                        */
                        if ($record->level == 2) {
                            $booking->update([
                                'status' => 'approved',
                                'current_level' => 2,
                            ]);
                        }

                        app(ActivityLogService::class)->log(
                            $user,
                            'Approval',
                            'approved',
                            'Booking ' .
                                $booking->booking_code .
                                ' disetujui pada level ' .
                                $record->level .
                                '.'
                        );
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Booking?')
                    ->form([
                        \Filament\Forms\Components\Textarea::make(
                            'notes'
                        )
                            ->label('Alasan Penolakan')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function (
                        BookingApproval $record,
                        array $data
                    ): void {
                        $user = Filament::auth()->user();

                        if (!$user instanceof User) {
                            return;
                        }

                        if ($record->approver_id !== $user->id) {
                            return;
                        }

                        if ($record->status !== 'menunggu') {
                            return;
                        }

                        $record->update([
                            'status' => 'ditolak',
                            'notes' => $data['notes'],
                            'acted_at' => now(),
                        ]);

                        $booking = $record->booking;

                        if (!$booking) {
                            return;
                        }

                        $booking->update([
                            'status' => 'rejected',
                        ]);

                        app(ActivityLogService::class)->log(
                            $user,
                            'Approval',
                            'rejected',
                            'Booking ' .
                                $booking->booking_code .
                                ' ditolak pada level ' .
                                $record->level .
                                '. Alasan: ' .
                                $data['notes']
                        );
                    }),
            ])

            ->defaultSort(
                'created_at',
                'asc'
            );
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingApprovals::route('/'),
            'view' => Pages\ViewBookingApproval::route(
                '/{record}'
            ),
        ];
    }
}