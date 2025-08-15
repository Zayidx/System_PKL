<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class PrakerinTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Ambil data yang diperlukan
        $siswaNIS = DB::table('siswa')->pluck('nis')->toArray();
        $pembimbingSekolahNIP = DB::table('pembimbing_sekolah')->pluck('nip_pembimbing_sekolah')->toArray();
        $pembimbingPerusahaanID = DB::table('pembimbing_perusahaan')->pluck('id_pembimbing')->toArray();
        $perusahaanID = DB::table('perusahaan')->pluck('id_perusahaan')->toArray();
        $kepalaProgramNIP = DB::table('kepala_program')->pluck('nip_kepala_program')->toArray();
        $staffHubinNIP = DB::table('staff_hubin')->pluck('nip_staff')->toArray();
        
        if (empty($siswaNIS) || empty($pembimbingSekolahNIP) || empty($pembimbingPerusahaanID) || 
            empty($perusahaanID) || empty($kepalaProgramNIP)) {
            $this->command->info('Data yang diperlukan tidak tersedia. Seeder tidak dijalankan.');
            return;
        }
        
        $prakerinData = [];
        $pengajuanData = [];
        
        // Membuat data prakerin dengan tanggal mulai yang berbeda untuk testing
        for ($i = 0; $i < 50; $i++) {
            // Tentukan periode secara acak
            $periode = $faker->randomElement(['today', '3days', '7days', '1month', '1year', 'older']);
            
            // Tentukan tanggal mulai berdasarkan periode
            $tanggalMulai = match($periode) {
                'today' => Carbon::today(),
                '3days' => Carbon::now()->subDays($faker->numberBetween(1, 3)),
                '7days' => Carbon::now()->subDays($faker->numberBetween(4, 7)),
                '1month' => Carbon::now()->subMonths($faker->numberBetween(1, 2)),
                '1year' => Carbon::now()->subYear()->addMonths($faker->numberBetween(1, 11)),
                'older' => Carbon::now()->subYears($faker->numberBetween(2, 5)),
            };
            
            // Tanggal selesai 3-6 bulan setelah tanggal mulai
            $tanggalSelesai = clone $tanggalMulai;
            $tanggalSelesai->addMonths($faker->numberBetween(3, 6));
            
            // Pilih NIS siswa secara acak
            $nisSiswa = $faker->randomElement($siswaNIS);
            
            // Pilih perusahaan secara acak
            $idPerusahaan = $faker->randomElement($perusahaanID);
            
            // Pilih pembimbing sekolah secara acak
            $nipPembimbingSekolah = $faker->randomElement($pembimbingSekolahNIP);
            
            // Pilih pembimbing perusahaan berdasarkan perusahaan
            $pembimbingPerusahaan = DB::table('pembimbing_perusahaan')
                ->where('id_perusahaan', $idPerusahaan)
                ->first();
            
            if (!$pembimbingPerusahaan) {
                // Jika tidak ada pembimbing perusahaan untuk perusahaan ini, buat yang baru
                $pembimbingPerusahaanUser = DB::table('users')->insertGetId([
                    'username' => $faker->userName,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'roles_id' => DB::table('roles')->where('name', 'pembimbingperusahaan')->first()->id,
                ]);
                
                $pembimbingPerusahaanID = DB::table('pembimbing_perusahaan')->insertGetId([
                    'id_perusahaan' => $idPerusahaan,
                    'user_id' => $pembimbingPerusahaanUser,
                    'nama' => $faker->name,
                    'no_hp' => $faker->e164PhoneNumber,
                    'email' => $faker->unique()->safeEmail,
                ]);
            } else {
                $pembimbingPerusahaanID = $pembimbingPerusahaan->id_pembimbing;
            }
            
            // Pilih kepala program secara acak
            $nipKepalaProgram = $faker->randomElement($kepalaProgramNIP);
            
            // Pilih staff hubin secara acak (bisa null)
            $nipStaff = $staffHubinNIP ? $faker->randomElement($staffHubinNIP) : null;
            
            // Tentukan status pengajuan (sebagian besar diterima karena sudah ada prakerin)
            $statusPengajuan = $faker->randomElement([
                'diterima_perusahaan', 'diterima_perusahaan', 'diterima_perusahaan', // 3x lebih sering
                'pending', 'ditolak_perusahaan'
            ]);
            
            // Buat data pengajuan terlebih dahulu
            $pengajuanID = DB::table('pengajuan')->insertGetId([
                'nis_siswa' => $nisSiswa,
                'id_perusahaan' => $idPerusahaan,
                'nip_kepala_program' => $nipKepalaProgram,
                'nip_staff' => $nipStaff,
                'status_pengajuan' => $statusPengajuan,
                'bukti_penerimaan' => $faker->optional(0.7)->imageUrl(),
                'token' => $faker->optional(0.3)->sha256,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
                'link_cv' => $faker->optional(0.6)->url,
                'created_at' => $tanggalMulai->copy()->subWeeks($faker->numberBetween(1, 4)), // Pengajuan dibuat sebelum prakerin dimulai
                'updated_at' => $tanggalMulai->copy()->subWeeks($faker->numberBetween(1, 4)),
            ]);
            
            // Buat data prakerin
            $prakerinData[] = [
                'nis_siswa' => $nisSiswa,
                'nip_pembimbing_sekolah' => $nipPembimbingSekolah,
                'id_pembimbing_perusahaan' => $pembimbingPerusahaanID,
                'id_perusahaan' => $idPerusahaan,
                'nip_kepala_program' => $nipKepalaProgram,
                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
                'keterangan' => $faker->sentence,
                'status_prakerin' => $faker->randomElement(['aktif', 'selesai', 'dibatalkan']),
            ];
        }
        
        // Insert data prakerin
        DB::table('prakerin')->insert($prakerinData);
        
        $this->command->info('Berhasil membuat ' . count($prakerinData) . ' data prakerin untuk testing.');
        $this->command->info('Setiap prakerin dilengkapi dengan riwayat pengajuan yang sesuai.');
        $this->command->info('Data mencakup berbagai periode: hari ini, 3 hari terakhir, 7 hari terakhir, 1 bulan terakhir, 1 tahun terakhir, dan lebih lama.');
    }
}