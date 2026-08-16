<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VehicleType::create([
            'name' => 'Mobil Penumpang',
            'category' => 'orang',
        ]);

        VehicleType::create([
            'name' => 'Kendaraan Barang',
            'category' => 'barang',
        ]);
    }
}
