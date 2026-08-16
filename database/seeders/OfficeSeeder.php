<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\Region;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jatim = Region::where('code', 'JATIM')->firstOrFail();
        $jabar = Region::where('code', 'JABAR')->firstOrFail();

        Office::create([
            'region_id' => $jatim->id,
            'name' => 'Nanickel Kantor Pusat',
            'type' => 'pusat',
            'address' => 'Lowokwaru, Malang, Jawa Timur',
        ]);

        Office::create([
            'region_id' => $jabar->id,
            'name' => 'Nanickel Cabang Bekasi',
            'type' => 'cabang',
            'address' => 'Bekasi Barat, Bekasi, Jawa Barat',
        ]);

        Office::create([
            'region_id' => $jatim->id,
            'name' => 'Nanickel Tambang Gresik',
            'type' => 'tambang',
            'address' => 'Kawasan Industri JIIPE, Gresik, Jawa Timur',
        ]);

        Office::create([
            'region_id' => $jatim->id,
            'name' => 'Nanickel Tambang Surabaya',
            'type' => 'tambang',
            'address' => 'Tanjung Perak, Surabaya, Jawa Timur',
        ]);

        Office::create([
            'region_id' => $jatim->id,
            'name' => 'Nanickel Tambang Banyuwangi',
            'type' => 'tambang',
            'address' => 'Ketapang, Banyuwangi, Jawa Timur',
        ]);

        Office::create([
            'region_id' => $jabar->id,
            'name' => 'Nanickel Tambang Karawang',
            'type' => 'tambang',
            'address' => 'Kawasan Industri KNIC, Karawang, Jawa Barat',
        ]);

        Office::create([
            'region_id' => $jabar->id,
            'name' => 'Nanickel Tambang Bekasi',
            'type' => 'tambang',
            'address' => 'Cikarang Pusat, Bekasi, Jawa Barat',
        ]);

        Office::create([
            'region_id' => $jabar->id,
            'name' => 'Nanickel Tambang Bandung',
            'type' => 'tambang',
            'address' => 'Soekarno-Hatta, Bandung, Jawa Barat',
        ]);

    }
}
