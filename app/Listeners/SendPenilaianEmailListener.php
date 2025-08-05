<?php

namespace App\Listeners;

use App\Events\PrakerinSelesaiEvent;
use App\Models\Kompetensi;
use Illuminate\Support\Facades\Mail;
use App\Mail\PenilaianFormEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPenilaianEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PrakerinSelesaiEvent $event): void
    {
        $prakerin = $event->prakerin;
        
        try {
            \Log::info('Event PrakerinSelesaiEvent triggered', [
                'prakerin_id' => $prakerin->id_prakerin,
                'siswa_nis' => $prakerin->siswa->nis ?? 'N/A'
            ]);

            // Ambil data yang diperlukan
            $pembimbingPerusahaan = $prakerin->pembimbingPerusahaan;
            $siswa = $prakerin->siswa;
            $perusahaan = $prakerin->perusahaan;
            
            // Cek email perusahaan
            if (!$perusahaan || !$perusahaan->email_perusahaan) {
                \Log::warning('Perusahaan tidak ditemukan atau tidak memiliki email', [
                    'prakerin_id' => $prakerin->id_prakerin,
                    'perusahaan_id' => $prakerin->id_perusahaan
                ]);
                return;
            }

            // Ambil kompetensi berdasarkan jurusan siswa
            $kompetensi = Kompetensi::where('id_jurusan', $siswa->id_jurusan)->get();
            
            if ($kompetensi->isEmpty()) {
                \Log::warning('Kompetensi tidak ditemukan untuk jurusan', [
                    'jurusan_id' => $siswa->id_jurusan,
                    'prakerin_id' => $prakerin->id_prakerin
                ]);
                return;
            }

            // Buat token unik untuk form penilaian
            $token = \Str::random(64);
            
            // Simpan token ke cache untuk validasi
            \Cache::put("penilaian_token_{$token}", [
                'prakerin_id' => $prakerin->id_prakerin,
                'nis_siswa' => $siswa->nis,
                'pembimbing_id' => $pembimbingPerusahaan->id_pembimbing,
                'expires_at' => now()->addDays(7) // Token berlaku 7 hari
            ], now()->addDays(7));

            // Kirim email ke perusahaan
            Mail::to($perusahaan->email_perusahaan)
                ->send(new PenilaianFormEmail($prakerin, $siswa, $perusahaan, $pembimbingPerusahaan, $kompetensi, $token));

            \Log::info('Email form penilaian berhasil dikirim via Event', [
                'prakerin_id' => $prakerin->id_prakerin,
                'siswa_nis' => $siswa->nis,
                'perusahaan_email' => $perusahaan->email_perusahaan,
                'perusahaan_nama' => $perusahaan->nama_perusahaan,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            \Log::error('Error mengirim email form penilaian via Event', [
                'prakerin_id' => $prakerin->id_prakerin,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
} 