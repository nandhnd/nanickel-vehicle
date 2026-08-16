<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FuelLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingOperationService
{
    /**
     * APPROVED -> ONGOING
     */
    public function start(
        Booking $booking,
        int $startOdometer,
        User $admin
    ): Booking {
        if (!$admin->isAdmin()) {
            throw ValidationException::withMessages([
                'authorization' => 'Hanya admin yang dapat memulai booking.',
            ]);
        }

        return DB::transaction(function () use (
            $booking,
            $startOdometer
        ) {
            /*
             * Pastikan status booking benar.
             */
            if ($booking->status !== 'approved') {
                throw ValidationException::withMessages([
                    'status' =>
                        'Booking hanya dapat dimulai jika sudah approved.',
                ]);
            }

            /*
             * Lock kendaraan selama proses.
             */
            $vehicle = $booking->vehicle()
                ->lockForUpdate()
                ->first();

            if (!$vehicle) {
                throw ValidationException::withMessages([
                    'vehicle' =>
                        'Kendaraan tidak ditemukan.',
                ]);
            }

            /*
             * Kendaraan harus tersedia.
             */
            if ($vehicle->status !== 'tersedia') {
                throw ValidationException::withMessages([
                    'vehicle' =>
                        'Kendaraan sedang tidak tersedia.',
                ]);
            }

            /*
             * Start odometer tidak boleh lebih kecil
             * dari odometer terakhir kendaraan.
             */
            if ($startOdometer < $vehicle->last_odometer) {
                throw ValidationException::withMessages([
                    'start_odometer' =>
                        'Start odometer tidak boleh lebih kecil dari odometer terakhir kendaraan (' .
                        $vehicle->last_odometer .
                        ').',
                ]);
            }

            /*
             * Lock driver.
             */
            $driver = null;

            if ($booking->driver_id) {
                $driver = $booking->driver()
                    ->lockForUpdate()
                    ->first();

                if (!$driver) {
                    throw ValidationException::withMessages([
                        'driver' =>
                            'Driver tidak ditemukan.',
                    ]);
                }

                if ($driver->status !== 'tersedia') {
                    throw ValidationException::withMessages([
                        'driver' =>
                            'Driver sedang tidak tersedia.',
                    ]);
                }
            }

            /*
             * Update booking.
             */
            $booking->update([
                'start_odometer' => $startOdometer,
                'status' => 'ongoing',
            ]);

            /*
             * Kendaraan digunakan.
             */
            $vehicle->update([
                'status' => 'dipakai',
            ]);

            /*
             * Driver bertugas.
             */
            if ($driver) {
                $driver->update([
                    'status' => 'bertugas',
                ]);
            }

            return $booking->fresh();
        });
    }

    /**
     * ONGOING -> COMPLETED
     */
    public function complete(
        Booking $booking,
        int $endOdometer,
        User $admin
    ): Booking {
        if (!$admin->isAdmin()) {
            throw ValidationException::withMessages([
                'authorization' =>
                    'Hanya admin yang dapat menyelesaikan booking.',
            ]);
        }

        return DB::transaction(function () use (
            $booking,
            $endOdometer
        ) {
            if ($booking->status !== 'ongoing') {
                throw ValidationException::withMessages([
                    'status' =>
                        'Booking hanya dapat diselesaikan jika sedang ongoing.',
                ]);
            }

            if ($booking->start_odometer === null) {
                throw ValidationException::withMessages([
                    'start_odometer' =>
                        'Start odometer belum diisi.',
                ]);
            }

            if ($endOdometer < $booking->start_odometer) {
                throw ValidationException::withMessages([
                    'end_odometer' =>
                        'End odometer tidak boleh lebih kecil dari start odometer.',
                ]);
            }

            $vehicle = $booking->vehicle()
                ->lockForUpdate()
                ->first();

            if (!$vehicle) {
                throw ValidationException::withMessages([
                    'vehicle' =>
                        'Kendaraan tidak ditemukan.',
                ]);
            }

            if ($vehicle->status !== 'dipakai') {
                throw ValidationException::withMessages([
                    'vehicle' =>
                        'Status kendaraan tidak sesuai dengan booking ongoing.',
                ]);
            }

            $booking->update([
                'end_odometer' => $endOdometer,
                'status' => 'completed',
            ]);

            $vehicle->update([
                'last_odometer' => $endOdometer,
                'status' => 'tersedia',
            ]);

            if ($booking->driver_id) {
                $driver = $booking->driver()
                    ->lockForUpdate()
                    ->first();

                if ($driver) {
                    $driver->update([
                        'status' => 'tersedia',
                    ]);
                }
            }

            return $booking->fresh();
        });
    }
}