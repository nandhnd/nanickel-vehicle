<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::create([
            'name' => 'Jawa Timur',
            'code' => 'JATIM',
        ]);

        Region::create([
            'name' => 'Jawa Barat',
            'code' => 'JABAR',
        ]);
    }
}
