<?php

namespace App\Filament\Resources\RentalCompanyResource;

use App\Filament\Resources\RentalCompanyResource\Pages;
use App\Models\RentalCompany;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RentalCompanyResource extends Resource
{
    protected static ?string $model = RentalCompany::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Rental Company';

    protected static ?string $modelLabel = 'Rental Company';

    protected static ?string $pluralModelLabel = 'Rental Companies';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 5;

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
                Forms\Components\Section::make('Informasi Perusahaan Rental')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(30),
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
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),

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
                    ->before(function (RentalCompany $record) {
                        if ($record->vehicles()->exists()) {
                            throw new \Exception(
                                'Perusahaan rental tidak dapat dihapus karena masih memiliki kendaraan.'
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
            'index' => Pages\ListRentalCompanies::route('/'),
            'create' => Pages\CreateRentalCompany::route('/create'),
            'edit' => Pages\EditRentalCompany::route('/{record}/edit'),
        ];
    }
}