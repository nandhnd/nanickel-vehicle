<?php

namespace App\Filament\Resources\ActivityLogResource;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables;

class ActivityLogResource extends Resource
{
    protected static ?string $model =
        ActivityLog::class;

    protected static ?string $navigationIcon =
        'heroicon-o-clock';

    protected static ?string $navigationLabel =
        'Activity Logs';

    protected static ?string $modelLabel =
        'Activity Log';

    protected static ?string $pluralModelLabel =
        'Activity Logs';

    protected static ?string $navigationGroup =
        'System';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->isAdmin();
    }

    public static function table(
        Tables\Table $table
    ): Tables\Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(
                    'created_at'
                )
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'user.name'
                )
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'module'
                )
                    ->label('Module')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'action'
                )
                    ->label('Action')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make(
                    'description'
                )
                    ->label('Description')
                    ->wrap()
                    ->searchable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('module')
                    ->label('Module')
                    ->options([
                        'Booking' => 'Booking',
                        'Approval' => 'Approval',
                        'Fuel Log' => 'Fuel Log',
                        'Service Schedule' =>
                            'Service Schedule',
                        'Vehicle' => 'Vehicle',
                        'Driver' => 'Driver',
                        'User' => 'User',
                    ]),

                Tables\Filters\SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'started' => 'Started',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'deleted' => 'Deleted',
                    ]),
            ])

            ->actions([])

            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}