<?php

namespace App\Filament\Resources\VehicleResource;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Vehicles';

    protected static ?string $modelLabel = 'Vehicle';

    protected static ?string $pluralModelLabel = 'Vehicles';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

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
                Forms\Components\Section::make('Identitas Kendaraan')
                    ->schema([
                        Forms\Components\TextInput::make('plate_number')
                            ->label('Nomor Polisi')
                            ->required()
                            ->unique(
                                table: 'vehicles',
                                column: 'plate_number',
                                ignoreRecord: true,
                            )
                            ->maxLength(20),

                        Forms\Components\TextInput::make('brand')
                            ->label('Merk')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) date('Y') + 1)
                            ->required(),

                        Forms\Components\Select::make('vehicle_type_id')
                            ->label('Vehicle Type')
                            ->relationship(
                                name: 'vehicleType',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('office_id')
                            ->label('Office')
                            ->relationship(
                                name: 'office',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Kepemilikan')
                    ->schema([
                        Forms\Components\Select::make('ownership')
                            ->label('Kepemilikan')
                            ->options([
                                'milik_sendiri' => 'Milik Sendiri',
                                'sewa' => 'Sewa',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('rental_company_id')
                            ->label('Perusahaan Rental')
                            ->relationship(
                                name: 'rentalCompany',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->visible(
                                fn (Forms\Get $get): bool =>
                                    $get('ownership') === 'sewa'
                            )
                            ->required(
                                fn (Forms\Get $get): bool =>
                                    $get('ownership') === 'sewa'
                            )
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Spesifikasi Operasional')
                    ->schema([

                        Forms\Components\TextInput::make('last_odometer')
                            ->label('Odometer Terakhir')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'dipakai' => 'Dipakai',
                                'maintenance' => 'Maintenance',
                                'tidak_aktif' => 'Tidak Aktif',
                            ])
                            ->required()
                            ->default('tersedia'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label('No. Polisi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand')
                    ->label('Merk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vehicleType.name')
                    ->label('Tipe')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('office.name')
                    ->label('Office')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ownership')
                    ->label('Kepemilikan')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'milik_sendiri' => 'Milik Sendiri',
                            'sewa' => 'Sewa',
                            default => $state,
                        }
                    ),

                Tables\Columns\TextColumn::make('rentalCompany.name')
                    ->label('Perusahaan Rental')
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('last_odometer')
                    ->label('Odometer')
                    ->numeric(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'tersedia' => 'Tersedia',
                            'dipakai' => 'Dipakai',
                            'maintenance' => 'Maintenance',
                            'tidak_aktif' => 'Tidak Aktif',
                            default => $state,
                        }
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->before(function (Vehicle $record) {
                        if (
                            $record->bookings()->exists()
                            || $record->fuelLogs()->exists()
                            || $record->serviceSchedules()->exists()
                        ) {
                            throw new \Exception(
                                'Kendaraan tidak dapat dihapus karena sudah memiliki data transaksi atau riwayat.'
                            );
                        }
                    }),
            ])
            ->defaultSort('plate_number');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}