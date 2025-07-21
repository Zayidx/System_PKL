<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\KepalaProgram;
use App\Models\KepalaSekolah;
use App\Models\PembimbingPerusahaan;
use App\Models\PembimbingSekolah;
use App\Models\Pengajuan;
use App\Models\Perusahaan;
use App\Models\Prakerin;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\StaffHubin;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PklSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan lokalisasi Indonesia

        DB::transaction(function () use ($faker) {
            $this->command->info('Memulai PklSeeder...');

            // 1. MEMBUAT ROLE & USER UTAMA
            $this->command->info('Membuat Role dan User utama...');
            $adminRole = Role::firstOrCreate(['name' => 'superadmin'], ['keterangan' => 'Administrator Sistem']);
            $userRole = Role::firstOrCreate(['name' => 'user'], ['keterangan' => 'Pengguna Biasa']);

            $adminUser = User::updateOrCreate(
                ['username' => 'superadmin'],
                [
                    'email' => 'faridx0236@gmail.com',
                    'password' => Hash::make('password'),
                    'foto' => 'profile.jpg',
                    'roles_id' => $adminRole->id,
                ]
            );
            $this->command->info('User Superadmin telah dibuat/diperbarui.');

            // 2. MEMBUAT DATA MASTER SEKOLAH
            $this->command->info('Membuat data master sekolah...');

            $angkatan = Angkatan::create(['tahun' => '2024']);
            $jurusanData = [
                ['nama_jurusan_lengkap' => 'Rekayasa Perangkat Lunak', 'nama_jurusan_singkat' => 'RPL'],
                ['nama_jurusan_lengkap' => 'Teknik Komputer dan Jaringan', 'nama_jurusan_singkat' => 'TKJ'],
                ['nama_jurusan_lengkap' => 'Desain Komunikasi Visual', 'nama_jurusan_singkat' => 'DKV'],
            ];
            $jurusans = collect();
            foreach ($jurusanData as $data) {
                $jurusans->push(Jurusan::create($data));
            }
            $this->command->info(count($jurusanData) . ' Jurusan telah dibuat.');

            $gurus = collect();
            for ($i = 0; $i < 10; $i++) {
                $gurus->push(Guru::create(['nama_guru' => $faker->name('male')]));
            }
            $this->command->info($gurus->count() . ' Guru telah dibuat.');

            $kepalaPrograms = collect();
            foreach ($jurusans as $jurusan) {
                $guruKaprog = $gurus->random();
                $kepalaPrograms->push(KepalaProgram::create([
                    'nip_guru' => $guruKaprog->nip_guru,
                    'id_jurusan' => $jurusan->id_jurusan,
                    'nama_kepala_program' => $guruKaprog->nama_guru,
                ]));
            }
            $this->command->info($kepalaPrograms->count() . ' Kepala Program telah dibuat.');

            $staffHubin = StaffHubin::create(['user_id' => $adminUser->id, 'nama_staff' => $faker->name]);
            KepalaSekolah::create([
                'nama_kepala_sekolah' => $faker->name('male'),
                'jabatan' => 'Kepala Sekolah',
                'nip_kepsek' => $faker->numerify('19#########-####-##-###')
            ]);
            $this->command->info('Staff Hubin dan Kepala Sekolah telah dibuat.');

            $waliKelasCollection = collect();
            for ($i = 0; $i < 5; $i++) {
                $waliKelasCollection->push(WaliKelas::create([
                    'user_id' => $adminUser->id,
                    'nama_wali_kelas' => $gurus->random()->nama_guru,
                ]));
            }
            $this->command->info($waliKelasCollection->count() . ' Wali Kelas telah dibuat.');

            $kelasCollection = collect();
            foreach ($jurusans as $jurusan) {
                for ($i = 1; $i <= 2; $i++) {
                    $kelasCollection->push(Kelas::create([
                        'nama_kelas' => $jurusan->nama_jurusan_singkat . '-' . $i,
                        'tingkat_kelas' => '12',
                        'nip_wali_kelas' => $waliKelasCollection->random()->nip_wali_kelas,
                        'id_jurusan' => $jurusan->id_jurusan,
                        'id_angkatan' => $angkatan->id_angkatan,
                    ]));
                }
            }
            $this->command->info($kelasCollection->count() . ' Kelas telah dibuat.');

            $siswas = collect();
            $this->command->info('Membuat 100 data Siswa...');
            for ($i = 0; $i < 100; $i++) {
                $kelas = $kelasCollection->random();
                $userSiswa = User::create([
                    'username' => $faker->unique()->userName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password'),
                    'foto' => 'profile.jpg',
                    'roles_id' => $userRole->id,
                ]);
                $siswas->push(Siswa::create([
                    'nis' => $faker->unique()->numerify('##########'),
                    'user_id' => $userSiswa->id,
                    'id_kelas' => $kelas->id_kelas,
                    'id_jurusan' => $kelas->id_jurusan,
                    'nama_siswa' => $faker->name,
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $faker->date(),
                ]));
            }
            $this->command->info($siswas->count() . ' Siswa telah berhasil dibuat.');

            $pembimbingSekolahs = collect();
            foreach ($gurus as $guru) {
                $pembimbingSekolahs->push(PembimbingSekolah::create([
                    'user_id' => $adminUser->id,
                    'nama_pembimbing_sekolah' => $guru->nama_guru,
                ]));
            }
            $this->command->info($pembimbingSekolahs->count() . ' Pembimbing Sekolah telah dibuat.');

            // 3. MEMBUAT DATA MASTER PERUSAHAAN
            $this->command->info('Membuat 20 data Perusahaan...');
            $perusahaans = collect();
            for ($i = 0; $i < 20; $i++) {
                $perusahaans->push(Perusahaan::create([
                    'nama_perusahaan' => $faker->company,
                    'alamat_perusahaan' => $faker->address,
                    'email_perusahaan' => $faker->unique()->companyEmail,
                ]));
            }
            $this->command->info($perusahaans->count() . ' Perusahaan telah berhasil dibuat.');

            $pembimbingPerusahaans = collect();
            foreach ($perusahaans as $perusahaan) {
                for ($j = 0; $j < 2; $j++) {
                    $pembimbingPerusahaans->push(PembimbingPerusahaan::create([
                        'id_perusahaan' => $perusahaan->id_perusahaan,
                        'nama' => $faker->name,
                        // FIX: Menggunakan e164PhoneNumber agar sesuai dengan batasan 17 karakter
                        'no_hp' => $faker->e164PhoneNumber,
                    ]));
                }
            }
            $this->command->info($pembimbingPerusahaans->count() . ' Pembimbing Perusahaan telah dibuat.');

            // 4. MEMBUAT DATA PRAKERIN UNTUK 50 SISWA
            $this->command->info('Membuat 50 data Prakerin...');
            $siswaPrakerin = $siswas->random(50);
            foreach ($siswaPrakerin as $siswa) {
                $perusahaan = $perusahaans->random();
                Pengajuan::create([
                    'nis_siswa' => $siswa->nis,
                    'id_perusahaan' => $perusahaan->id_perusahaan,
                    'nip_kepala_program' => $kepalaPrograms->where('id_jurusan', $siswa->id_jurusan)->first()->nip_kepala_program,
                    'nip_staff' => $staffHubin->nip_staff,
                    'status_pengajuan' => 'diterima',
                    'bukti_penerimaan' => 'surat_penerimaan.pdf',
                ]);

                Prakerin::create([
                    'nis_siswa' => $siswa->nis,
                    'nip_pembimbing_sekolah' => $pembimbingSekolahs->random()->nip_pembimbing_sekolah,
                    'id_pembimbing_perusahaan' => $pembimbingPerusahaans->where('id_perusahaan', $perusahaan->id_perusahaan)->random()->id_pembimbing,
                    'id_perusahaan' => $perusahaan->id_perusahaan,
                    'nip_kepala_program' => $kepalaPrograms->where('id_jurusan', $siswa->id_jurusan)->first()->nip_kepala_program,
                    'tanggal_mulai' => $faker->dateTimeBetween('-1 month', '+1 month'),
                    'tanggal_selesai' => $faker->dateTimeBetween('+2 months', '+4 months'),
                    'keterangan' => 'Siswa sedang melaksanakan prakerin',
                ]);
            }
            $this->command->info($siswaPrakerin->count() . ' data Prakerin telah berhasil dibuat.');
            $this->command->info('PklSeeder selesai dijalankan.');
        });
    }
}
