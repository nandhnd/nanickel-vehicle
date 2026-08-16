<?php

namespace App\Filament\Resources\VehicleTypeResource;

use App\Filament\Resources\VehicleTypeResource\Pages;
use App\Models\User;
use App\Models\VehicleType;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleTypeResource extends Resource
{
    protected static ?string $model = VehicleType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Vehicle Type';

    protected static ?string $modelLabel = 'Vehicle Type';

    protected static ?string $pluralModelLabel = 'Vehicle Types';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 4;

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
                Forms\Components\Section::make('Informasi Vehicle Type')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Tipe Kendaraan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'orang' => 'Orang',
                                'barang' => 'Barang',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'orang' => 'Orang',
                            'barang' => 'Barang',
                            default => $state,
                        }
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('vehicles_count')
                    ->label('Jumlah Kendaraan')
                    ->counts('vehicles'),

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
                    ->before(function (VehicleType $record) {
                        if ($record->vehicles()->exists()) {
                            throw new \Exception(
                                'Vehicle Type tidak dapat dihapus karena masih digunakan oleh kendaraan.'
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
            'index' => Pages\ListVehicleTypes::route('/'),
            'create' => Pages\CreateVehicleType::route('/create'),
            'edit' => Pages\EditVehicleType::route('/{record}/edit'),
        ];
    }
}