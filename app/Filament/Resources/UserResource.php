<?php

namespace App\Filament\Resources\UserResource;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

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
                Forms\Components\Section::make('Informasi User')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true,
                            )
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(
                                fn (?string $state): bool => filled($state)
                            )
                            ->minLength(8),

                        Forms\Components\TextInput::make('position')
                            ->label('Position')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'admin' => 'Admin',
                                'approver' => 'Approver',
                            ])
                            ->required()
                            ->live()
                            ->default('approver'),

                        Forms\Components\Select::make('approval_level')
                            ->label('Approval Level')
                            ->options([
                                1 => 'Level 1',
                                2 => 'Level 2',
                            ])
                            ->visible(
                                fn (Forms\Get $get): bool =>
                                    $get('role') === 'approver'
                            )
                            ->required(
                                fn (Forms\Get $get): bool =>
                                    $get('role') === 'approver'
                            )
                            ->nullable(),

                        Forms\Components\Select::make('office_id')
                            ->label('Office')
                            ->relationship(
                                name: 'office',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->inline(false),
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

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Position')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'admin' => 'Admin',
                            'approver' => 'Approver',
                            default => $state,
                        }
                    ),

                Tables\Columns\TextColumn::make('approval_level')
                    ->label('Level')
                    ->formatStateUsing(
                        fn ($state, User $record): string =>
                            $record->isApprover()
                                ? "Level {$state}"
                                : '-'
                    ),

                Tables\Columns\TextColumn::make('office.name')
                    ->label('Office')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

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
                    ->before(function (User $record) {
                        $currentUser = Filament::auth()->user();

                        if ($currentUser?->id === $record->id) {
                            throw new \Exception(
                                'Anda tidak dapat menghapus akun sendiri.'
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}