<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat data Super Admin
        User::create([
            'name' => 'Super Admin',
            'role' => 'super_admin',
            'email' => 'adewahidin21@gmail.com',
            'password' => Hash::make('12345678'),
            'no_hp' => '08123456789',
            'pekerjaan' => 'Super Admin',
            'alamat_lengkap' => 'Alamat Super Admin',
        ]);
    }
}
