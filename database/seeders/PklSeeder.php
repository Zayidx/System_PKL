<?php

namespace Database\Seeders;

use App\Models\Angkatan;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\KepalaProgram;
use App\Models\KepalaSekolah;
use App\Models\Kompetensi;
use App\Models\KontakPerusahaan;
use App\Models\Monitoring;
use App\Models\PembimbingPerusahaan;
use App\Models\PembimbingSekolah;
use App\Models\Pengajuan;
use App\Models\Penilaian;
use App\Models\Perusahaan;
use App\Models\Prakerin;
use App\Models\PresensiSiswa;
use App\Models\Role;
use App\Models\Sertifikat;
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
        $faker = Faker::create('id_ID');

        DB::transaction(function () use ($faker) {
            $this->command->info('Memulai PklSeeder...');

            // 1. MEMBUAT ROLE & USER UTAMA
            $this->command->info('Membuat Role...');
            $superadminRole = Role::firstOrCreate(['name' => 'superadmin'], ['keterangan' => 'Administrator Sistem']);
            $adminRole = Role::firstOrCreate(['name' => 'admin'], ['keterangan' => 'Administrator (Guru, dll)']);
            $userRole = Role::firstOrCreate(['name' => 'user'], ['keterangan' => 'Pengguna (Siswa)']);
            $waliKelasRole = Role::firstOrCreate(['name' => 'walikelas'], ['keterangan' => 'Wali Kelas']);

            $superadminUser = User::firstOrCreate(['email' => 'superadmin@sekolah.sch.id'], ['username' => 'superadmin', 'password' => Hash::make('password'), 'roles_id' => $superadminRole->id]);
            $this->command->info('User Superadmin telah dibuat.');

            User::firstOrCreate(
                ['email' => 'faridgaming15@gmail.com'],
                ['username' => 'Farid Indrawan', 'password' => Hash::make('indrawan0236'), 'roles_id' => $superadminRole->id]
            );
            $this->command->info('User Farid Gaming (superadmin) telah dibuat.');

            // 2. MEMBUAT DATA MASTER SEKOLAH
            $this->command->info('Membuat data master sekolah...');
            $angkatan = Angkatan::firstOrCreate(['tahun' => now()->year]);
            
            $jurusanData = [['nama_jurusan_lengkap' => 'Rekayasa Perangkat Lunak', 'nama_jurusan_singkat' => 'RPL'], ['nama_jurusan_lengkap' => 'Teknik Komputer dan Jaringan', 'nama_jurusan_singkat' => 'TKJ'], ['nama_jurusan_lengkap' => 'Desain Komunikasi Visual', 'nama_jurusan_singkat' => 'DKV']];
            foreach ($jurusanData as $data) { Jurusan::firstOrCreate($data); }
            $jurusans = Jurusan::where('nama_jurusan_singkat', '!=', 'N/A')->get();
            $this->command->info($jurusans->count() . ' Jurusan telah dibuat.');

            $kompetensis = collect();
            foreach ($jurusans as $jurusan) {
                for ($i = 1; $i <= 5; $i++) {
                    $kompetensis->push(Kompetensi::create(['id_jurusan' => $jurusan->id_jurusan, 'nama_kompetensi' => 'Kompetensi ' . $jurusan->nama_jurusan_singkat . ' ' . $i]));
                }
            }
            $this->command->info($kompetensis->count() . ' Kompetensi Keahlian telah dibuat.');

            $gurus = collect();
            for ($i = 0; $i < 15; $i++) {
                $userGuru = User::create(['username' => $faker->unique()->userName, 'email' => $faker->unique()->safeEmail, 'password' => Hash::make('password'), 'roles_id' => $adminRole->id]);
                $gurus->push(Guru::create(['user_id' => $userGuru->id, 'nama_guru' => $userGuru->username, 'kontak_guru' => $faker->e164PhoneNumber]));
            }
            $this->command->info($gurus->count() . ' Guru (dengan akun admin) telah dibuat.');

            $kepalaPrograms = collect();
            foreach ($jurusans as $jurusan) { $kepalaPrograms->push(KepalaProgram::create(['nip_guru' => $gurus->random()->nip_guru, 'id_jurusan' => $jurusan->id_jurusan, 'nama_kepala_program' => $gurus->random()->nama_guru])); }
            $this->command->info($kepalaPrograms->count() . ' Kepala Program telah dibuat.');

            $this->command->info('Membuat 5 Wali Kelas dengan akun User (role: walikelas)...');
            $waliKelasCollection = collect();
            for ($i = 0; $i < 5; $i++) {
                $userWaliKelas = User::create(['username' => $faker->unique()->userName, 'email' => $faker->unique()->safeEmail, 'password' => Hash::make('password'), 'roles_id' => $waliKelasRole->id]);
                
                // PERBAIKAN: Hapus 'nip_wali_kelas' dari create(). Biarkan database yang mengisinya secara otomatis.
                $waliKelasCollection->push(WaliKelas::create([
                    'user_id' => $userWaliKelas->id, 
                    'nama_wali_kelas' => $userWaliKelas->username
                ]));
            }
            $this->command->info($waliKelasCollection->count() . ' Wali Kelas (dengan akun walikelas) telah dibuat.');

            $pembimbingSekolahs = collect();
            foreach ($gurus->random(5) as $guruPembimbing) { $pembimbingSekolahs->push(PembimbingSekolah::create(['user_id' => $guruPembimbing->user_id, 'nama_pembimbing_sekolah' => $guruPembimbing->nama_guru])); }
            $this->command->info($pembimbingSekolahs->count() . ' Pembimbing Sekolah telah dibuat.');

            $kepalaSekolah = KepalaSekolah::create(['nama_kepala_sekolah' => $faker->name('male'), 'jabatan' => 'Kepala Sekolah', 'nip_kepsek' => $faker->numerify('19#########-####-##-###')]);
            $staffHubin = StaffHubin::create(['user_id' => $superadminUser->id, 'nama_staff' => $faker->name]);
            $this->command->info('Staff Hubin dan Kepala Sekolah telah dibuat.');

            $kelasCollection = collect();
            foreach ($jurusans as $jurusan) {
                for ($i = 1; $i <= 2; $i++) {
                    $kelasCollection->push(Kelas::create(['nama_kelas' => 'XII ' . $jurusan->nama_jurusan_singkat . ' ' . $i, 'tingkat_kelas' => 'XII', 'nip_wali_kelas' => $waliKelasCollection->random()->nip_wali_kelas, 'id_jurusan' => $jurusan->id_jurusan, 'id_angkatan' => $angkatan->id_angkatan]));
                }
            }
            $jurusanDefault = Jurusan::firstOrCreate(['nama_jurusan_lengkap' => 'Belum Ditentukan', 'nama_jurusan_singkat' => 'N/A']);
            $kelasCollection->push(Kelas::firstOrCreate(['nama_kelas' => 'N/A'], ['tingkat_kelas' => 'N/A', 'nip_wali_kelas' => $waliKelasCollection->first()->nip_wali_kelas, 'id_jurusan' => $jurusanDefault->id_jurusan, 'id_angkatan' => $angkatan->id_angkatan]));
            $this->command->info($kelasCollection->count() . ' Kelas telah dibuat.');
            
            $siswas = collect();
            for ($i = 0; $i < 100; $i++) {
                $kelas = $kelasCollection->where('nama_kelas', '!=', 'N/A')->random();
                $userSiswa = User::create(['username' => $faker->unique()->userName, 'email' => $faker->unique()->safeEmail, 'password' => Hash::make('password'), 'roles_id' => $userRole->id]);
                $siswas->push(Siswa::create(['nis' => $faker->unique()->numerify('##########'), 'user_id' => $userSiswa->id, 'id_kelas' => $kelas->id_kelas, 'id_jurusan' => $kelas->id_jurusan, 'nama_siswa' => $userSiswa->username, 'tempat_lahir' => $faker->city, 'tanggal_lahir' => $faker->date(), 'kontak_siswa' => $faker->e164PhoneNumber]));
            }
            $this->command->info($siswas->count() . ' Siswa (dengan akun user) telah dibuat.');

            // 3. MEMBUAT DATA MASTER PERUSAHAAN
            $this->command->info('Membuat data Perusahaan...');
            $perusahaans = collect();
            for ($i = 0; $i < 20; $i++) {
                $perusahaans->push(Perusahaan::create(['nama_perusahaan' => $faker->company, 'alamat_perusahaan' => $faker->address, 'email_perusahaan' => $faker->unique()->companyEmail, 'kontak_perusahaan' => $faker->e164PhoneNumber]));
            }
            $this->command->info($perusahaans->count() . ' Perusahaan telah dibuat.');

            foreach ($perusahaans as $perusahaan) {
                KontakPerusahaan::create(['id_perusahaan' => $perusahaan->id_perusahaan, 'kontak_perusahaan' => $faker->e164PhoneNumber]);
                for ($j = 0; $j < 2; $j++) {
                    PembimbingPerusahaan::create(['id_perusahaan' => $perusahaan->id_perusahaan, 'nama' => $faker->name, 'no_hp' => $faker->e164PhoneNumber]);
                }
            }
            $this->command->info('Kontak dan Pembimbing Perusahaan telah dibuat.');

            // 4. MEMBUAT DATA TRANSAKSIONAL PKL UNTUK 50 SISWA
            $this->command->info('Membuat data transaksional PKL untuk 50 siswa...');
            $siswaPrakerin = $siswas->random(50);
            foreach ($siswaPrakerin as $siswa) {
                $perusahaan = $perusahaans->random();
                $pembimbingPerusahaan = PembimbingPerusahaan::where('id_perusahaan', $perusahaan->id_perusahaan)->inRandomOrder()->first();
                $pembimbingSekolah = $pembimbingSekolahs->random();
                $kepalaProgram = $kepalaPrograms->where('id_jurusan', $siswa->id_jurusan)->first();

                if ($kepalaProgram && $pembimbingPerusahaan) {
                    Pengajuan::create(['nis_siswa' => $siswa->nis, 'id_perusahaan' => $perusahaan->id_perusahaan, 'nip_kepala_program' => $kepalaProgram->nip_kepala_program, 'nip_staff' => $staffHubin->nip_staff, 'status_pengajuan' => 'diterima', 'bukti_penerimaan' => 'surat_penerimaan.pdf']);
                    Prakerin::create(['nis_siswa' => $siswa->nis, 'nip_pembimbing_sekolah' => $pembimbingSekolah->nip_pembimbing_sekolah, 'id_pembimbing_perusahaan' => $pembimbingPerusahaan->id_pembimbing, 'id_perusahaan' => $perusahaan->id_perusahaan, 'nip_kepala_program' => $kepalaProgram->nip_kepala_program, 'tanggal_mulai' => $faker->dateTimeBetween('-1 month', 'now'), 'tanggal_selesai' => $faker->dateTimeBetween('+2 months', '+4 months'), 'keterangan' => 'Siswa sedang melaksanakan prakerin']);
                    PresensiSiswa::create(['id_pembimbing_perusahaan' => $pembimbingPerusahaan->id_pembimbing, 'tanggal_kehadiran' => now()->toDateString(), 'jam_masuk' => '08:00:00', 'jam_pulang' => '17:00:00', 'kegiatan' => 'Mempelajari alur kerja', 'keterangan' => 'Hadir', 'status' => 'Disetujui']);
                    Monitoring::create(['id_perusahaan' => $perusahaan->id_perusahaan, 'nip_pembimbing_sekolah' => $pembimbingSekolah->nip_pembimbing_sekolah, 'id_kepsek' => $kepalaSekolah->id_kepsek, 'tanggal' => now()->toDateString(), 'catatan' => 'Kunjungan berjalan lancar', 'verifikasi' => 'Terverifikasi']);
                    
                    $penilaian = Penilaian::create(['nis_siswa' => $siswa->nis, 'id_pemb_perusahaan' => $pembimbingPerusahaan->id_pembimbing]);
                    $kompetensiJurusan = $kompetensis->where('id_jurusan', $siswa->id_jurusan)->random(3);
                    foreach ($kompetensiJurusan as $kompetensi) {
                        DB::table('nilai')->insert(['id_penilaian' => $penilaian->id_penilaian, 'id_kompetensi' => $kompetensi->id_kompetensi, 'nilai' => $faker->numberBetween(75, 95)]);
                    }
                    Sertifikat::create(['id_penilaian' => $penilaian->id_penilaian, 'file_sertifikat' => 'sertifikat_' . $siswa->nis . '.pdf']);
                }
            }
            $this->command->info($siswaPrakerin->count() . ' data transaksional PKL telah berhasil dibuat.');
            $this->command->info('PklSeeder selesai dijalankan dengan sukses!');
        });
    }
}
