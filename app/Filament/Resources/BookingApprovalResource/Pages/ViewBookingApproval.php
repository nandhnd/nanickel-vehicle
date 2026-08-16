<?php

namespace App\Filament\Resources\BookingApprovalResource\Pages;

use App\Filament\Resources\BookingApprovalResource\BookingApprovalResource;
use App\Models\BookingApproval;
use App\Services\BookingApprovalService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Gate;
use RuntimeException;

class ViewBookingApproval extends ViewRecord
{
    protected static string $resource =
        BookingApprovalResource::class;

    protected function beforeFill(): void
    {
        $user = \Filament\Facades\Filament::auth()->user();

        if (
            !$user ||
            $this->record->approver_id !== $user->id
        ) {
            abort(403);
        }
    }
        
    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'menunggu'
                )
                ->requiresConfirmation()
                ->modalHeading('Setujui Booking')
                ->modalDescription(
                    'Apakah Anda yakin ingin menyetujui booking ini?'
                )
                ->action(function (): void {
                    try {
                        app(BookingApprovalService::class)
                            ->approve(
                                $this->record,
                                \Filament\Facades\Filament::auth()->user()
                            );

                        Notification::make()
                            ->title('Booking berhasil disetujui')
                            ->success()
                            ->send();

                        $this->redirect(
                            BookingApprovalResource::getUrl(
                                'index'
                            )
                        );
                    } catch (RuntimeException $e) {
                        Notification::make()
                            ->title('Approval gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        $this->record->status === 'menunggu'
                )
                ->form([
                    Forms\Components\Textarea::make('notes')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->minLength(3)
                        ->rows(4),
                ])
                ->modalHeading('Tolak Booking')
                ->modalSubmitActionLabel('Tolak Booking')
                ->action(function (array $data): void {
                    try {
                        app(BookingApprovalService::class)
                            ->reject(
                                $this->record,
                                \Filament\Facades\Filament::auth()->user(),
                                $data['notes']
                            );

                        Notification::make()
                            ->title('Booking ditolak')
                            ->success()
                            ->send();

                        $this->redirect(
                            BookingApprovalResource::getUrl(
                                'index'
                            )
                        );
                    } catch (RuntimeException $e) {
                        Notification::make()
                            ->title('Penolakan gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}