<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan Query Builder untuk memasukkan data
        // Ini adalah cara yang efisien untuk seeding data statis
        DB::table('roles')->insert([
            [
                'name' => 'superadmin',
                'keterangan' => 'Administrator Sistem',
            ],
            [
                'name' => 'user',
                'keterangan' => 'Pengguna Biasa',
            ],
           
            [
                'name' => 'walikelas',
                'keterangan' => 'Wali Kelas',
            ],
        ]);
    }
}
