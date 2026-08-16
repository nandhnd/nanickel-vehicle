<?php

namespace App\Filament\Resources\ServiceScheduleResource;

use App\Filament\Resources\ServiceScheduleResource\Pages;
use App\Models\ServiceSchedule;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class ServiceScheduleResource extends Resource
{
    protected static ?string $model = ServiceSchedule::class;

    protected static ?string $navigationIcon =
        'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel =
        'Service Schedule';

    protected static ?string $modelLabel =
        'Service Schedule';

    protected static ?string $pluralModelLabel =
        'Service Schedules';

    protected static ?string $navigationGroup =
        'Maintenance';

    protected static ?int $navigationSort = 1;

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

            Forms\Components\TextInput::make('service_type')
                ->label('Jenis Service')
                ->required()
                ->maxLength(255)
                ->placeholder('Contoh: Ganti Oli'),

            Forms\Components\DatePicker::make(
                'scheduled_date'
            )
                ->label('Tanggal Service')
                ->required(),

            Forms\Components\DatePicker::make(
                'completed_date'
            )
                ->label('Tanggal Selesai')
                ->nullable(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'terjadwal' => 'Terjadwal',
                    'selesai' => 'Selesai',
                    'terlambat' => 'Terlambat',
                ])
                ->default('terjadwal')
                ->required(),
        ]);
    }

    public static function table(
        Tables\Table $table
    ): Tables\Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(
                    'vehicle.plate_number'
                )
                    ->label('Kendaraan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'service_type'
                )
                    ->label('Jenis Service')
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'scheduled_date'
                )
                    ->label('Jadwal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'completed_date'
                )
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'status'
                )
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            'terjadwal' => 'warning',
                            'selesai' => 'success',
                            'terlambat' => 'danger',
                            default => 'gray',
                        }
                    ),

                Tables\Columns\TextColumn::make(
                    'createdBy.name'
                )
                    ->label('Dibuat Oleh'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'terjadwal' => 'Terjadwal',
                        'selesai' => 'Selesai',
                        'terlambat' => 'Terlambat',
                    ]),

                Tables\Filters\SelectFilter::make('vehicle_id')
                    ->label('Kendaraan')
                    ->relationship(
                        'vehicle',
                        'plate_number'
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort(
                'scheduled_date',
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
            'index' => Pages\ListServiceSchedules::route('/'),
            'create' => Pages\CreateServiceSchedule::route(
                '/create'
            ),
            'edit' => Pages\EditServiceSchedule::route(
                '/{record}/edit'
            ),
        ];
    }
}