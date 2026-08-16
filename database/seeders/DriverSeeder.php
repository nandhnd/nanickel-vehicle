<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Office;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pusat = Office::where('name', 'Nanickel Kantor Pusat')->firstOrFail();
        $cabang = Office::where('name', 'Nanickel Cabang Bekasi')->firstOrFail();

        Driver::create([
            'name' => 'Eno Bening',
            'license_number' => '3171012805950001',
            'license_type' => 'A',
            'office_id' => $pusat->id,
            'status' => 'tersedia',
        ]);

        Driver::create([
            'name' => 'Muhammad Raka',
            'license_number' => '3273221510880003',
            'license_type' => 'B1',
            'office_id' => $pusat->id,
            'status' => 'tersedia',
        ]);

        Driver::create([
            'name' => 'Dedi Setia',
            'license_number' => '3578050212820005',
            'license_type' => 'B2',
            'office_id' => $cabang->id,
            'status' => 'tersedia',
        ]);
    }
}
