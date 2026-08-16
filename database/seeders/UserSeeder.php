<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Office;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pusat = Office::where('name', 'Nanickel Kantor Pusat')->firstOrFail();
        $cabang = Office::where('name', 'Nanickel Cabang Bekasi')->firstOrFail();

        User::create([
            'name' => 'Fahmi Ramadhan',
            'email' => 'adminkp1@nanickel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'position' => 'Administrator',
            'approval_level' => null,
            'office_id' => $pusat->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Eko Prasetyo',
            'email' => 'ekoprasetyo@nanickel.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
            'position' => 'Supervisor',
            'approval_level' => 1,
            'office_id' => $pusat->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Hendra Wijaya',
            'email' => 'hendra.wijaya@nanickel.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
            'position' => 'Manager',
            'approval_level' => 2,
            'office_id' => $pusat->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'admin',
            'email' => 'admincb1@nanickel.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'position' => 'Administrator',
            'approval_level' => null,
            'office_id' => $cabang->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Rian Hidayat',
            'email' => 'rianhidayat@nanickel.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
            'position' => 'Supervisor',
            'approval_level' => 1,
            'office_id' => $cabang->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewilestari@nanickel.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
            'position' => 'Manager',
            'approval_level' => 2,
            'office_id' => $cabang->id,
            'is_active' => true,
        ]);
    }
}
