<?php

namespace App\Filament\Resources\DriverResource;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Drivers';

    protected static ?string $modelLabel = 'Driver';

    protected static ?string $pluralModelLabel = 'Drivers';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 7;

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
                Forms\Components\Section::make('Informasi Driver')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Driver')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('license_number')
                            ->label('Nomor SIM')
                            ->required()
                            ->unique(
                                table: 'drivers',
                                column: 'license_number',
                                ignoreRecord: true,
                            )
                            ->maxLength(100),

                        Forms\Components\Select::make('license_type')
                            ->label('Jenis SIM')
                            ->options([
                                'A' => 'A',
                                'B1' => 'B1',
                                'B2' => 'B2',
                            ])
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

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'tersedia' => 'Tersedia',
                                'bertugas' => 'Bertugas',
                                'cuti' => 'Cuti',
                            ])
                            ->required()
                            ->default('tersedia'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_number')
                    ->label('Nomor SIM')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_type')
                    ->label('Jenis SIM')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('office.name')
                    ->label('Office')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'tersedia' => 'Tersedia',
                            'bertugas' => 'Bertugas',
                            'cuti' => 'Cuti',
                            default => $state,
                        }
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Jumlah Booking')
                    ->counts('bookings'),

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
                    ->before(function (Driver $record) {
                        if ($record->bookings()->exists()) {
                            throw new \Exception(
                                'Driver tidak dapat dihapus karena sudah memiliki riwayat booking.'
                            );
                        }
                    }),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}