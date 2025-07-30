<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\KepalaProgram;
use App\Models\KepalaSekolah;
use App\Models\Kompetensi;
use App\Models\PembimbingPerusahaan;
use App\Models\PembimbingSekolah;
use App\Models\Perusahaan;
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
    public function run(): void
    {
        // Inisialisasi Faker hanya sekali di sini
        $faker = Faker::create('id_ID');

        DB::transaction(function () use ($faker) {
            $this->command->info('Memulai PklSeeder...');

            // 1. MEMBUAT SEMUA ROLE YANG DIBUTUHKAN
            $this->command->info('Membuat Role...');
            $superadminRole = Role::firstOrCreate(['name' => 'superadmin'], ['keterangan' => 'Administrator Sistem']);
            $adminRole = Role::firstOrCreate(['name' => 'admin'], ['keterangan' => 'Administrator (Guru)']);
            $userRole = Role::firstOrCreate(['name' => 'user'], ['keterangan' => 'Pengguna (Siswa)']);
            $waliKelasRole = Role::firstOrCreate(['name' => 'walikelas'], ['keterangan' => 'Wali Kelas']);
            $pembimbingPerusahaanRole = Role::firstOrCreate(['name' => 'pembimbingperusahaan'], ['keterangan' => 'Pembimbing Perusahaan']);
            $pembimbingSekolahRole = Role::firstOrCreate(['name' => 'pembimbingsekolah'], ['keterangan' => 'Pembimbing dari Sekolah']);
            $staffHubinRole = Role::firstOrCreate(['name' => 'staffhubin'], ['keterangan' => 'Staff Hubin']);
            $kepalaSekolahRole = Role::firstOrCreate(['name' => 'kepalasekolah'], ['keterangan' => 'Kepala Sekolah']);
            $kepalaProgramRole = Role::firstOrCreate(['name' => 'kepalaprogram'], ['keterangan' => 'Kepala Program']);

            // 2. MEMBUAT USER STATIS
            User::firstOrCreate(['email' => 'superadmin@sekolah.sch.id'], ['username' => 'superadmin', 'password' => Hash::make('password'), 'roles_id' => $superadminRole->id]);
            User::firstOrCreate(['email' => 'faridgaming15@gmail.com'], ['username' => 'Farid Indrawan', 'password' => Hash::make('indrawan0236'), 'roles_id' => $superadminRole->id]);
            $this->command->info('User statis (superadmin) telah dibuat.');

            // 3. MEMBUAT DATA MASTER SEKOLAH
            $this->command->info('Membuat data master sekolah...');
            $angkatan = Angkatan::firstOrCreate(['tahun' => now()->year]);
            
            $jurusanData = [['nama_jurusan_lengkap' => 'Rekayasa Perangkat Lunak', 'nama_jurusan_singkat' => 'RPL'], ['nama_jurusan_lengkap' => 'Teknik Komputer dan Jaringan', 'nama_jurusan_singkat' => 'TKJ'], ['nama_jurusan_lengkap' => 'Desain Komunikasi Visual', 'nama_jurusan_singkat' => 'DKV']];
            foreach ($jurusanData as $data) { Jurusan::firstOrCreate($data); }
            $jurusans = Jurusan::where('nama_jurusan_singkat', '!=', 'N/A')->get();
            $this->command->info($jurusans->count() . ' Jurusan telah dibuat.');

            // Membuat entitas-entitas yang memiliki akun user
            $gurus = $this->createUsersWithProfile($faker, 5, $adminRole, Guru::class, ['nama_guru' => 'username', 'kontak_guru' => fn() => $faker->e164PhoneNumber]);
            $this->command->info($gurus->count() . ' Guru (role: admin) telah dibuat.');
            
            $waliKelasCollection = $this->createUsersWithProfile($faker, 5, $waliKelasRole, WaliKelas::class, ['nama_wali_kelas' => 'username']);
            $this->command->info($waliKelasCollection->count() . ' Wali Kelas (role: walikelas) telah dibuat.');

            $pembimbingSekolahs = $this->createUsersWithProfile($faker, 5, $pembimbingSekolahRole, PembimbingSekolah::class, [
                'nama_pembimbing_sekolah' => 'username',
                'kontak_pembimbing_sekolah' => fn() => $faker->e164PhoneNumber
            ]);
            $this->command->info($pembimbingSekolahs->count() . ' Pembimbing Sekolah (role: pembimbingsekolah) telah dibuat.');

            // Membuat akun Staff Hubin statis
            $this->command->info('Membuat akun Staff Hubin statis (vanjirgua@gmail.com)...');
            $staffHubinUserStatis = User::firstOrCreate(
                ['email' => 'vanjirgua@gmail.com'],
                [
                    'username' => 'vanjirgua',
                    'password' => Hash::make('indrawan0236'),
                    'roles_id' => $staffHubinRole->id
                ]
            );
            $staffHubinStatis = StaffHubin::firstOrCreate(
                ['user_id' => $staffHubinUserStatis->id],
                ['nama_staff' => $staffHubinUserStatis->username]
            );

            // Membuat Staff Hubin Faker
            $staffHubins = $this->createUsersWithProfile($faker, 1, $staffHubinRole, StaffHubin::class, ['nama_staff' => 'username']);
            $staffHubins->push($staffHubinStatis); // Menambahkan staff hubin statis ke koleksi
            $this->command->info($staffHubins->count() . ' Staff Hubin (role: staffhubin) telah dibuat.');


            $kepalaSekolahs = $this->createUsersWithProfile($faker, 1, $kepalaSekolahRole, KepalaSekolah::class, ['nama_kepala_sekolah' => 'username', 'jabatan' => 'Kepala Sekolah', 'nip_kepsek' => fn() => $faker->numerify('19#########-####-##-###')]);
            $this->command->info($kepalaSekolahs->count() . ' Kepala Sekolah (role: kepalasekolah) telah dibuat.');

            $kepalaPrograms = collect();
            foreach ($jurusans as $jurusan) {
                $kaprogUser = User::create(['username' => $faker->unique()->userName, 'email' => $faker->unique()->safeEmail, 'password' => Hash::make('password'), 'roles_id' => $kepalaProgramRole->id]);
                $kepalaPrograms->push(KepalaProgram::create(['user_id' => $kaprogUser->id, 'id_jurusan' => $jurusan->id_jurusan, 'nama_kepala_program' => $kaprogUser->username]));
            }
            $this->command->info($kepalaPrograms->count() . ' Kepala Program (role: kepalaprogram) telah dibuat.');

            // Membuat Kelas
            $kelasCollection = collect();
            foreach ($jurusans as $jurusan) {
                for ($i = 1; $i <= 2; $i++) {
                    $kelasCollection->push(Kelas::create(['nama_kelas' => 'XII ' . $jurusan->nama_jurusan_singkat . ' ' . $i, 'tingkat_kelas' => 'XII', 'nip_wali_kelas' => $waliKelasCollection->random()->nip_wali_kelas, 'id_jurusan' => $jurusan->id_jurusan, 'id_angkatan' => $angkatan->id_angkatan]));
                }
            }
            $jurusanDefault = Jurusan::firstOrCreate(['nama_jurusan_lengkap' => 'Belum Ditentukan', 'nama_jurusan_singkat' => 'N/A']);
            $kelasDefault = Kelas::firstOrCreate(['nama_kelas' => 'N/A'], ['tingkat_kelas' => 'N/A', 'nip_wali_kelas' => $waliKelasCollection->first()->nip_wali_kelas, 'id_jurusan' => $jurusanDefault->id_jurusan, 'id_angkatan' => $angkatan->id_angkatan]);
            $kelasCollection->push($kelasDefault);
            $this->command->info($kelasCollection->count() . ' Kelas telah dibuat.');

            // Membuat akun siswa statis
            $siswaUser = User::firstOrCreate(['email' => 'faridx0236@gmail.com'], ['username' => 'Farid Siswa', 'password' => Hash::make('indrawan0236'), 'roles_id' => $userRole->id]);
            Siswa::firstOrCreate(['user_id' => $siswaUser->id], ['nis' => '1234567890', 'id_kelas' => $kelasCollection->where('nama_kelas', '!=', 'N/A')->random()->id_kelas, 'id_jurusan' => Jurusan::where('nama_jurusan_singkat', 'RPL')->first()->id_jurusan, 'nama_siswa' => $siswaUser->username, 'tempat_lahir' => 'Jakarta', 'tanggal_lahir' => '2006-08-17', 'kontak_siswa' => '081234567890']);
            $this->command->info('User Siswa Farid (user) telah dibuat.');
            
            // Membuat Siswa Faker
            $siswas = $this->createUsersWithProfile($faker, 25, $userRole, Siswa::class, ['nis' => fn() => $faker->unique()->numerify('##########'), 'nama_siswa' => 'username', 'id_kelas' => fn() => $kelasCollection->where('nama_kelas', '!=', 'N/A')->random()->id_kelas, 'id_jurusan' => fn($kelas) => $kelas->id_jurusan, 'tempat_lahir' => fn() => $faker->city, 'tanggal_lahir' => fn() => $faker->date(), 'kontak_siswa' => fn() => $faker->e164PhoneNumber]);
            $this->command->info($siswas->count() . ' Siswa (role: user) telah dibuat.');

            // 4. MEMBUAT DATA PERUSAHAAN
            $this->command->info('Membuat data Perusahaan...');
            $perusahaans = collect();
            for ($i = 0; $i < 20; $i++) {
                $perusahaans->push(Perusahaan::create([
                    'nama_perusahaan' => $faker->company,
                    'alamat_perusahaan' => $faker->address,
                    'email_perusahaan' => 'silfa0236@gmail.com',
                    'kontak_perusahaan' => $faker->e164PhoneNumber
                ]));
            }
            $this->command->info($perusahaans->count() . ' Perusahaan telah dibuat.');

            // Membuat Pembimbing Perusahaan (1 per perusahaan)
            $pembimbingPerusahaans = collect();
            foreach ($perusahaans as $perusahaan) {
                $pembimbingUser = User::create(['username' => $faker->unique()->userName, 'email' => $faker->unique()->safeEmail, 'password' => Hash::make('password'), 'roles_id' => $pembimbingPerusahaanRole->id]);
                $pembimbingPerusahaans->push(PembimbingPerusahaan::create([
                    'id_perusahaan' => $perusahaan->id_perusahaan, 
                    'user_id' => $pembimbingUser->id, 
                    'nama' => $pembimbingUser->username, 
                    'no_hp' => $faker->e164PhoneNumber,
                    'email' => $pembimbingUser->email
                ]));
            }
            $this->command->info($pembimbingPerusahaans->count() . ' Pembimbing Perusahaan (role: pembimbingperusahaan) telah dibuat.');

            // Mengassign pembimbing ke perusahaan
            $this->command->info('Mengassign pembimbing ke perusahaan...');
            
            // Ambil semua pembimbing sekolah dan perusahaan
            $pembimbingSekolahs = PembimbingSekolah::all();
            $pembimbingPerusahaans = PembimbingPerusahaan::all();
            
            // Assign pembimbing perusahaan ke perusahaan (1:1)
            foreach ($perusahaans as $index => $perusahaan) {
                $pembimbingPerusahaan = $pembimbingPerusahaans->where('id_perusahaan', $perusahaan->id_perusahaan)->first();
                if ($pembimbingPerusahaan) {
                    $perusahaan->update([
                        'id_pembimbing_perusahaan' => $pembimbingPerusahaan->id_pembimbing
                    ]);
                }
            }
            
            // Assign pembimbing sekolah ke beberapa perusahaan (1:many)
            // Setiap pembimbing sekolah bisa mengawasi 2-4 perusahaan
            foreach ($pembimbingSekolahs as $pembimbingSekolah) {
                $jumlahPerusahaan = $faker->numberBetween(2, 4);
                $perusahaanTerpilih = $perusahaans->random($jumlahPerusahaan);
                
                foreach ($perusahaanTerpilih as $perusahaan) {
                    $perusahaan->update([
                        'nip_pembimbing_sekolah' => $pembimbingSekolah->nip_pembimbing_sekolah
                    ]);
                }
            }
            
            $this->command->info('Pembimbing telah diassign ke perusahaan.');

            // CATATAN: Tidak membuat pengajuan atau prakerin dummy
            // Siswa akan berada dalam tahap awal tanpa pengajuan apapun
            $this->command->info('Siswa dibuat dalam tahap awal tanpa pengajuan atau prakerin.');

            $this->command->info('PklSeeder selesai dijalankan dengan sukses!');
        });
    }

    /**
     * Helper function to create users and their corresponding profiles.
     */
    private function createUsersWithProfile(\Faker\Generator $faker, int $count, Role $role, string $profileModel, array $profileAttributes): \Illuminate\Support\Collection
    {
        $collection = collect();
        $usedEmails = User::pluck('email')->toArray();
        for ($i = 0; $i < $count; $i++) {
            // Generate email unik
            $email = null;
            $try = 0;
            do {
                $try++;
                $email = $faker->unique()->safeEmail;
                if ($try > 5) {
                    $email = 'user_' . $role->name . '_' . $i . '@example.com';
                }
            } while (in_array($email, $usedEmails));
            $usedEmails[] = $email;

            $user = User::create([
                'username' => $faker->unique()->userName,
                'email' => $email,
                'password' => Hash::make('password'),
                'roles_id' => $role->id,
            ]);
            $attributes = ['user_id' => $user->id];
            foreach ($profileAttributes as $key => $value) {
                if (is_callable($value)) {
                    $relatedModel = null;
                    if ($key === 'id_jurusan' && isset($attributes['id_kelas'])) {
                         $relatedModel = Kelas::find($attributes['id_kelas']);
                    }
                    $attributes[$key] = $value($relatedModel);
                } elseif ($value === 'username') {
                    $attributes[$key] = $user->username;
                } else {
                    $attributes[$key] = $value;
                }
            }
            $collection->push($profileModel::create($attributes));
        }
        return $collection;
    }

    /**
     * Helper function to create prakerin data
     */
    private function createPrakerinData(\Faker\Generator $faker): void
    {
        $this->command->info('Membuat data Prakerin...');
        $prakerins = collect();
        $kepalaPrograms = KepalaProgram::all();
        $perusahaanDenganPembimbing = Perusahaan::whereNotNull('nip_pembimbing_sekolah')->get();
        $pembimbingSekolahs = PembimbingSekolah::all();
        $pembimbingPerusahaans = PembimbingPerusahaan::all();
        $siswaNIS = Siswa::pluck('nis')->toArray();
        
        // Membuat prakerin dummy dengan NIS yang benar
        for ($i = 0; $i < 30; $i++) {
            $prakerins->push(DB::table('prakerin')->insert([
                'nis_siswa' => $faker->randomElement($siswaNIS),
                'nip_pembimbing_sekolah' => $pembimbingSekolahs->random()->nip_pembimbing_sekolah,
                'id_pembimbing_perusahaan' => $pembimbingPerusahaans->random()->id_pembimbing,
                'id_perusahaan' => $perusahaanDenganPembimbing->random()->id_perusahaan,
                'nip_kepala_program' => $kepalaPrograms->random()->nip_kepala_program,
                'tanggal_mulai' => $faker->dateTimeBetween('now', '+1 month'),
                'tanggal_selesai' => $faker->dateTimeBetween('+2 months', '+4 months'),
                'keterangan' => $faker->optional(0.6)->sentence,
                'status_prakerin' => $faker->randomElement(['aktif', 'selesai', 'dibatalkan'])
            ]));
        }
        $this->command->info('30 Prakerin dummy telah dibuat.');
    }
}
