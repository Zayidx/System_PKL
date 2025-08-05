<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Prakerin;
use App\Models\Kompetensi;
use App\Models\Penilaian;
use App\Models\Nilai;

class DebuggingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== MEMBUAT DATA DEBUGGING PENILAIAN ===');

        // 1. Pastikan Farid ada dengan status selesai
        $farid = Siswa::where('nama_siswa', 'like', '%Farid%')->first();
        if (!$farid) {
            $farid = new Siswa();
            $farid->nis = '1234567890';
            $farid->nama_siswa = 'Farid Siswa';
            $farid->id_jurusan = 1;
            $farid->id_kelas = 1;
            $farid->save();
            $this->command->info('Farid berhasil dibuat');
        } else {
            $this->command->info('Farid sudah ada: ' . $farid->nama_siswa);
        }

        // 2. Buat kompetensi untuk semua jurusan
        $jurusanIds = [1, 2, 3];
        foreach ($jurusanIds as $jurusanId) {
            $existingKompetensi = Kompetensi::where('id_jurusan', $jurusanId)->count();
            if ($existingKompetensi == 0) {
                for ($i = 1; $i <= 3; $i++) {
                    $kompetensi = new Kompetensi();
                    $kompetensi->id_jurusan = $jurusanId;
                    $kompetensi->nama_kompetensi = "Kompetensi Jurusan {$jurusanId} - {$i}";
                    $kompetensi->save();
                }
                $this->command->info("Kompetensi untuk jurusan {$jurusanId} berhasil dibuat");
            } else {
                $this->command->info("Kompetensi untuk jurusan {$jurusanId} sudah ada ({$existingKompetensi} kompetensi)");
            }
        }

        // 3. Buat prakerin selesai untuk Farid
        $existingPrakerin = Prakerin::where('nis_siswa', $farid->nis)
            ->where('status_prakerin', 'selesai')
            ->first();

        if (!$existingPrakerin) {
            $prakerin = new Prakerin();
            $prakerin->nis_siswa = $farid->nis;
            $prakerin->id_perusahaan = 1;
            $prakerin->id_pembimbing_perusahaan = 1;
            $prakerin->nip_pembimbing_sekolah = 1;
            $prakerin->nip_kepala_program = 1;
            $prakerin->tanggal_mulai = now()->subMonths(3);
            $prakerin->tanggal_selesai = now();
            $prakerin->status_prakerin = 'selesai';
            $prakerin->keterangan = 'Prakerin Farid untuk debugging penilaian';
            $prakerin->save();
            $this->command->info('Prakerin selesai untuk Farid berhasil dibuat');
        } else {
            $this->command->info('Prakerin selesai untuk Farid sudah ada');
        }

        // 4. Buat beberapa prakerin selesai lainnya untuk testing
        $siswaList = Siswa::where('nis', '!=', $farid->nis)->take(3)->get();
        foreach ($siswaList as $siswa) {
            $existingPrakerin = Prakerin::where('nis_siswa', $siswa->nis)
                ->where('status_prakerin', 'selesai')
                ->first();

            if (!$existingPrakerin) {
                $prakerin = new Prakerin();
                $prakerin->nis_siswa = $siswa->nis;
                $prakerin->id_perusahaan = 1;
                $prakerin->id_pembimbing_perusahaan = 1;
                $prakerin->nip_pembimbing_sekolah = 1;
                $prakerin->nip_kepala_program = 1;
                $prakerin->tanggal_mulai = now()->subMonths(2);
                $prakerin->tanggal_selesai = now()->subDays(5);
                $prakerin->status_prakerin = 'selesai';
                $prakerin->keterangan = 'Prakerin selesai untuk testing';
                $prakerin->save();
                $this->command->info("Prakerin selesai untuk {$siswa->nama_siswa} berhasil dibuat");
            }
        }

        $this->command->info('=== DATA DEBUGGING PENILAIAN SELESAI ===');
        $this->command->info('Farid: ' . $farid->nama_siswa . ' (NIS: ' . $farid->nis . ')');
        $this->command->info('Total Kompetensi: ' . Kompetensi::count());
        $this->command->info('Total Prakerin Selesai: ' . Prakerin::where('status_prakerin', 'selesai')->count());
    }
} 