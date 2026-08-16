<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RentalCompany;

class RentalCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RentalCompany::create([
            'name' => 'PT Rental Sejahtera',
            'contact_person' => 'Rudi Hartono',
            'phone' => '081234567890',
        ]);
    }
}
