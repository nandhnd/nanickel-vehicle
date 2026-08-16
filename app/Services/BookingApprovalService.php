<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingApprovalService
{
    
    // Membuat approval level 1 ketika booking dibuat.    
    public function createInitialApproval(
        Booking $booking
    ): BookingApproval {
        return DB::transaction(function () use ($booking) {

            return BookingApproval::create([
                'booking_id' => $booking->id,
                'approver_id' => $booking->approver_level_1_id,
                'level' => 1,
                'status' => 'menunggu',
            ]);
        });
    }
    
    // Menyetujui approval pada level tertentu.    
    public function approve(
        BookingApproval $approval,
        User $approver
    ): void {
        DB::transaction(function () use ($approval, $approver) {

            if ($approval->approver_id !== $approver->id) {
                throw new RuntimeException(
                    'Anda tidak memiliki hak untuk menyetujui approval ini.'
                );
            }

            if ($approval->status !== 'menunggu') {
                throw new RuntimeException(
                    'Approval ini sudah diproses.'
                );
            }

            $booking = $approval->booking;

            
            // Pastikan level yang diproses adalah current level booking.            
            if ($booking->current_level !== $approval->level) {
                throw new RuntimeException(
                    'Approval level ini tidak sedang aktif.'
                );
            }

            $approval->update([
                'status' => 'disetujui',
                'acted_at' => now(),
            ]);

            // Level 1 disetujui. Buat approval level 2.
            if ($approval->level === 1) {
                $this->createLevelTwoApproval($booking);
                return;
            }

            // Level 2 disetujui.Booking menjadi approved.
            if ($approval->level === 2) {
                $booking->update([
                    'status' => 'approved',
                    'current_level' => 2,
                ]);
            }
        });
    }

    // Menolak approval.
    public function reject(
        BookingApproval $approval,
        User $approver,
        ?string $notes = null
    ): void {
        DB::transaction(function () use (
            $approval,
            $approver,
            $notes
        ) {

            if ($approval->approver_id !== $approver->id) {
                throw new RuntimeException(
                    'Anda tidak memiliki hak untuk menolak approval ini.'
                );
            }

            if ($approval->status !== 'menunggu') {
                throw new RuntimeException(
                    'Approval ini sudah diproses.'
                );
            }

            $booking = $approval->booking;

            if ($booking->current_level !== $approval->level) {
                throw new RuntimeException(
                    'Approval level ini tidak sedang aktif.'
                );
            }

            $approval->update([
                'status' => 'ditolak',
                'notes' => $notes,
                'acted_at' => now(),
            ]);

            $booking->update([
                'status' => 'rejected',
            ]);
        });
    }

    // Membuat approval level 2 setelah approval level 1 disetujui.
    protected function createLevelTwoApproval(
        Booking $booking
    ): BookingApproval {
        $booking->update([
            'current_level' => 2,
        ]);

        return BookingApproval::create([
            'booking_id' => $booking->id,
            'approver_id' => $booking->approver_level_2_id,
            'level' => 2,
            'status' => 'menunggu',
        ]);
    }
}