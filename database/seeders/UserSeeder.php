<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID dari role yang sudah ada
        // Pastikan nama yang dicari SAMA PERSIS dengan yang ada di RolesSeeder.php
        $adminRole = Role::where('name', 'superadmin')->firstOrFail();
        $userRole = Role::where('name', 'user')->firstOrFail();

        // 2. Buat User Admin (Superadmin)
        User::create([
            'username' => 'superadmin',
            'email' => 'faridgaming15@gmail.com',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
            'foto' => 'profile.jpg',
            'roles_id' => $adminRole->id, // Gunakan ID dari role superadmin
        ]);

        // 3. Buat User Biasa
        User::create([
            'username' => 'johndoe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang aman
            'foto' => 'profile.jpg',
            'roles_id' => $userRole->id, // Gunakan ID dari role user
        ]);
    }
}
