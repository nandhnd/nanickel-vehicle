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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->string('plate_number')
                ->unique();

            $table->string('brand');
            $table->string('model');
            $table->year('year');

            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('ownership', [
                'milik_sendiri',
                'sewa',
            ]);

            $table->foreignId('rental_company_id')
                ->nullable()
                ->constrained('rental_companies')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('office_id')
                ->constrained('offices')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->unsignedInteger('capacity');

            $table->unsignedBigInteger('last_odometer')
                ->default(0);

            $table->enum('status', [
                'tersedia',
                'dibooking',
                'dipakai',
                'maintenance',
                'tidak_aktif',
            ])->default('tersedia');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
