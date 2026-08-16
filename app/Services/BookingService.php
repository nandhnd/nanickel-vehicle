<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function __construct(
        protected BookingCodeService $bookingCodeService,
        protected BookingApprovalService $approvalService,
    ) {
    }

    public function create(
        array $data,
        User $creator
    ): Booking {
        return DB::transaction(function () use (
            $data,
            $creator
        ) {
            $approverLevel1 = User::query()
                ->where('id', $data['approver_level_1_id'])
                ->where('role', 'approver')
                ->where('approval_level', 1)
                ->where('is_active', true)
                ->first();

            if (!$approverLevel1) {
                throw ValidationException::withMessages([
                    'approver_level_1_id' =>
                        'Approver level 1 tidak valid atau tidak aktif.',
                ]);
            }

            $approverLevel2 = User::query()
                ->where('id', $data['approver_level_2_id'])
                ->where('role', 'approver')
                ->where('approval_level', 2)
                ->where('is_active', true)
                ->first();

            if (!$approverLevel2) {
                throw ValidationException::withMessages([
                    'approver_level_2_id' =>
                        'Approver level 2 tidak valid atau tidak aktif.',
                ]);
            }

            $booking = Booking::create([
                'booking_code' => $this->bookingCodeService->generate(),

                'requester_name' => $data['requester_name'],

                'requester_office_id' =>
                    $data['requester_office_id'],

                'vehicle_id' =>
                    $data['vehicle_id'],

                'driver_id' =>
                    $data['driver_id'] ?? null,

                'purpose' =>
                    $data['purpose'],

                'destination' =>
                    $data['destination'],

                'start_datetime' =>
                    $data['start_datetime'],

                'end_datetime' =>
                    $data['end_datetime'],

                'approver_level_1_id' =>
                    $data['approver_level_1_id'],

                'approver_level_2_id' =>
                    $data['approver_level_2_id'],

                'status' => 'pending',

                'current_level' => 1,

                'created_by' => $creator->id,
            ]);

            /*
             * Approval level 1 langsung dibuat.
             */
            $this->approvalService
                ->createInitialApproval($booking);

            return $booking;
        });
    }
}