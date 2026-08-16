<?php

namespace App\Filament\Resources\FuelLogResource;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\FuelLog;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static ?string $navigationIcon =
        'heroicon-o-beaker';

    protected static ?string $navigationLabel =
        'Fuel Logs';

    protected static ?string $modelLabel =
        'Fuel Log';

    protected static ?string $pluralModelLabel =
        'Fuel Logs';

    protected static ?string $navigationGroup =
        'Operational';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->isAdmin();
    }

    public static function form(
        Forms\Form $form
    ): Forms\Form {
        return $form->schema([
            Forms\Components\Select::make('vehicle_id')
                ->label('Kendaraan')
                ->relationship(
                    'vehicle',
                    'plate_number'
                )
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\Select::make('booking_id')
                ->label('Booking')
                ->relationship(
                    'booking',
                    'booking_code'
                )
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText(
                    'Kosongkan jika pengisian BBM tidak terkait booking.'
                ),

            Forms\Components\DatePicker::make('fuel_date')
                ->label('Tanggal Pengisian')
                ->default(
                    now()->toDateString()
                )
                ->required(),

            Forms\Components\TextInput::make('liters')
                ->label('Jumlah Liter')
                ->numeric()
                ->minValue(0.01)
                ->required(),

            Forms\Components\TextInput::make('cost')
                ->label('Biaya')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(
                    'vehicle.plate_number'
                )
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'booking.booking_code'
                )
                    ->label('Booking')
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'fuel_date'
                )
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'liters'
                )
                    ->label('Liter')
                    ->suffix(' L')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'cost'
                )
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'recordedBy.name'
                )
                    ->label('Dicatat Oleh')
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'created_at'
                )
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Kendaraan')
                    ->relationship(
                        'vehicle',
                        'plate_number'
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fuel_date')
                    ->form([
                        Forms\Components\DatePicker::make(
                            'from'
                        )
                            ->label('Dari'),

                        Forms\Components\DatePicker::make(
                            'until'
                        )
                            ->label('Sampai'),
                    ])
                    ->query(
                        function (
                            $query,
                            array $data
                        ) {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn ($query, $date) =>
                                        $query->whereDate(
                                            'fuel_date',
                                            '>=',
                                            $date
                                        )
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn ($query, $date) =>
                                        $query->whereDate(
                                            'fuel_date',
                                            '<=',
                                            $date
                                        )
                                );
                        }
                    ),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])

            ->defaultSort(
                'fuel_date',
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
            'index' => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
            'edit' => Pages\EditFuelLog::route(
                '/{record}/edit'
            ),
        ];
    }
}