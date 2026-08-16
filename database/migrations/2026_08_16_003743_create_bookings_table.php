<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code')
                ->unique();

            $table->string('requester_name');

            $table->foreignId('requester_office_id')
                ->constrained('offices')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('purpose');
            $table->string('destination');

            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');

            $table->unsignedBigInteger('start_odometer')
                ->nullable();

            $table->unsignedBigInteger('end_odometer')
                ->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('pending');

            $table->unsignedTinyInteger('current_level')
                ->default(1);

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->index([
                'vehicle_id',
                'start_datetime',
                'end_datetime',
            ]);

            $table->index([
                'driver_id',
                'start_datetime',
                'end_datetime',
            ]);

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
