<?php

namespace App\Filament\Resources\OfficeResource;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Office';

    protected static ?string $modelLabel = 'Office';

    protected static ?string $pluralModelLabel = 'Office';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

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
                Forms\Components\Section::make('Informasi Office')
                    ->schema([
                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->relationship(
                                name: 'region',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nama Office')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Office')
                            ->options([
                                'pusat' => 'Pusat',
                                'cabang' => 'Cabang',
                                'tambang' => 'Tambang',
                            ])
                            ->required(),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('region.name')
                    ->label('Region')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Office')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'pusat' => 'Pusat',
                            'cabang' => 'Cabang',
                            'tambang' => 'Tambang',
                            default => $state,
                        }
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('User')
                    ->counts('users'),

                Tables\Columns\TextColumn::make('vehicles_count')
                    ->label('Kendaraan')
                    ->counts('vehicles'),

                Tables\Columns\TextColumn::make('drivers_count')
                    ->label('Driver')
                    ->counts('drivers'),

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
                    ->before(function (Office $record) {
                        if (
                            $record->users()->exists()
                            || $record->vehicles()->exists()
                            || $record->drivers()->exists()
                            || $record->bookings()->exists()
                        ) {
                            throw new \Exception(
                                'Office tidak dapat dihapus karena masih memiliki data terkait.'
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
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}