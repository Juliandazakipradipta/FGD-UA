<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin
        Admin::updateOrCreate(
            ['email' => 'admin@notulensi.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'scope' => 'all',
            ]
        );

        // 2. Admin Ulul Albab
        Admin::updateOrCreate(
            ['email' => 'ululalbab@notulensi.test'],
            [
                'name' => 'Admin ULUL ALBAB',
                'password' => Hash::make('UA123'),
                'scope' => 'ulul_albab',
            ]
        );

        // 3. Admin Perumnas 2
        Admin::updateOrCreate(
            ['email' => 'perumnas2@notulensi.test'],
            [
                'name' => 'Admin Perumnas 2',
                'password' => Hash::make('perumnas123'),
                'scope' => 'perumnas_2',
            ]
        );
    }
}
