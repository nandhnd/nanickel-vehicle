<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\RentalCompany;
use App\Models\Vehicle;
use App\Models\VehicleType;


class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $pusat = Office::where('name', 'Nanickel Kantor Pusat')->firstOrFail();

        $mobilPenumpang = VehicleType::where(
            'name',
            'Mobil Penumpang'
        )->firstOrFail();

        $kendaraanBarang = VehicleType::where(
            'name',
            'Kendaraan Barang'
        )->firstOrFail();

        $rental = RentalCompany::where(
            'name',
            'PT Rental Sejahtera'
        )->firstOrFail();

        Vehicle::create([
            'plate_number' => 'N 1234 AB',
            'brand' => 'Toyota',
            'model' => 'Innova',
            'year' => 2023,
            'vehicle_type_id' => $mobilPenumpang->id,
            'ownership' => 'milik_sendiri',
            'rental_company_id' => null,
            'office_id' => $pusat->id,
            'last_odometer' => 25000,
            'status' => 'tersedia',
        ]);

        Vehicle::create([
            'plate_number' => 'N 5678 CD',
            'brand' => 'Mitsubishi',
            'model' => 'Triton',
            'year' => 2022,
            'vehicle_type_id' => $kendaraanBarang->id,
            'ownership' => 'sewa',
            'rental_company_id' => $rental->id,
            'office_id' => $pusat->id,
            'last_odometer' => 42000,
            'status' => 'tersedia',
        ]);

        Vehicle::create([
            'plate_number' => 'N 9012 EF',
            'brand' => 'Toyota',
            'model' => 'Hilux',
            'year' => 2024,
            'vehicle_type_id' => $kendaraanBarang->id,
            'ownership' => 'milik_sendiri',
            'rental_company_id' => null,
            'office_id' => $pusat->id,
            'last_odometer' => 10000,
            'status' => 'tersedia',
        ]);
    }
}
