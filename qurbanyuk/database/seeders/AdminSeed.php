<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin1',
            'email' => 'admin1@email.com',
            'level' => 'user',
            'password' => Hash::make('123456'),
        ])->sendEmailVerificationNotification();
        // User::create([
        //     'name' => 'Admin2',
        //     'email' => 'admin2@email.com',
        //     'level' => 'admin',
        //     'password' => Hash::make('123456'),
        // ])->sendEmailVerificationNotification();
        // User::create([
        //     'name' => 'Admin3',
        //     'email' => 'admin3@email.com',
        //     'level' => 'admin',
        //     'password' => Hash::make('123456'),
        // ])->sendEmailVerificationNotification();
    }
}
