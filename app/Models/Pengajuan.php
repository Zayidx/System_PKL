<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Siswa;
use App\Models\Perusahaan;
use App\Models\Prakerin;
use App\Models\PembimbingSekolah;
use App\Models\KepalaProgram;

/*
|--------------------------------------------------------------------------
| Model: Pengajuan
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `pengajuan`.
| Berisi data pengajuan prakerin yang dilakukan oleh siswa.
|
*/
class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';
    public $timestamps = true; // Mengaktifkan timestamps created_at dan updated_at

    protected $fillable = [
        'nis_siswa',
        'id_perusahaan',
        'nip_kepala_program',
        'nip_staff',
        'status_pengajuan',
        'bukti_penerimaan',
        'tanggal_mulai', // kontrak PKL
        'tanggal_selesai', // kontrak PKL
        'link_cv', // link CV siswa
    ];

    protected static function booted()
    {
        static::updated(function ($pengajuan) {
            // Jika status pengajuan berubah menjadi diterima_perusahaan
            if ($pengajuan->isDirty('status_pengajuan') && $pengajuan->status_pengajuan === 'diterima_perusahaan') {
                // Hapus semua pengajuan lain untuk siswa ini
                Pengajuan::where('nis_siswa', $pengajuan->nis_siswa)
                    ->where('id_pengajuan', '!=', $pengajuan->id_pengajuan)
                    ->update(['status_pengajuan' => 'dibatalkan']);
                
                // Kirim notifikasi email ke perusahaan lain yang sudah menerima pengajuan
                $pengajuanLain = Pengajuan::where('nis_siswa', $pengajuan->nis_siswa)
                    ->where('id_pengajuan', '!=', $pengajuan->id_pengajuan)
                    ->where('status_pengajuan', '!=', 'dibatalkan')
                    ->get();
                
                foreach ($pengajuanLain as $pengajuanLainnya) {
                    // Kirim email notifikasi ke perusahaan
                    if ($pengajuanLainnya->perusahaan && $pengajuanLainnya->perusahaan->email_perusahaan) {
                        try {
                            \Mail::send('emails.siswa-diterima-di-perusahaan-lain', [
                                'perusahaan' => $pengajuanLainnya->perusahaan,
                                'siswa' => $pengajuan->siswa,
                                'perusahaanDiterima' => $pengajuan->perusahaan
                            ], function ($message) use ($pengajuanLainnya) {
                                $message->to($pengajuanLainnya->perusahaan->email_perusahaan)
                                       ->subject('Siswa Telah Diterima di Perusahaan Lain');
                            });
                        } catch (\Exception $e) {
                            \Log::error('Gagal mengirim email notifikasi', [
                                'error' => $e->getMessage(),
                                'email' => $pengajuanLainnya->perusahaan->email_perusahaan
                            ]);
                        }
                    }
                }
                
                // Cek apakah sudah ada prakerin aktif untuk siswa ini
                $existingPrakerin = Prakerin::where('nis_siswa', $pengajuan->nis_siswa)
                    ->where('status_prakerin', 'aktif')
                    ->first();

                if (!$existingPrakerin) {
                    try {
                        // Ambil pembimbing perusahaan dari perusahaan
                        $pembimbingPerusahaan = $pengajuan->perusahaan->pembimbingPerusahaan->first();
                        
                        // Ambil pembimbing sekolah dari perusahaan
                        $pembimbingSekolah = $pengajuan->perusahaan->pembimbingSekolah;
                        
                        // Ambil kepala program dari jurusan siswa
                        $kepalaProgram = KepalaProgram::where('id_jurusan', $pengajuan->siswa->id_jurusan)->first();
                        
                        // Pastikan semua data yang diperlukan ada
                        if (!$pembimbingSekolah || !$kepalaProgram) {
                            \Log::error('Data pembimbing sekolah atau kepala program tidak ditemukan untuk prakerin', [
                                'nis_siswa' => $pengajuan->nis_siswa,
                                'id_perusahaan' => $pengajuan->id_perusahaan
                            ]);
                            return; // Jangan buat prakerin jika data tidak lengkap
                        }
                        
                        // Buat prakerin baru
                        Prakerin::create([
                            'nis_siswa' => $pengajuan->nis_siswa,
                            'id_perusahaan' => $pengajuan->id_perusahaan,
                            'id_pembimbing_perusahaan' => $pembimbingPerusahaan->id_pembimbing ?? null,
                            'nip_pembimbing_sekolah' => $pembimbingSekolah->nip_pembimbing_sekolah,
                            'nip_kepala_program' => $kepalaProgram->nip_kepala_program,
                            'tanggal_mulai' => now(),
                            'tanggal_selesai' => now()->addMonths(3), // Default 3 bulan
                            'status_prakerin' => 'aktif',
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Error saat membuat prakerin otomatis', [
                            'error' => $e->getMessage(),
                            'nis_siswa' => $pengajuan->nis_siswa,
                            'id_perusahaan' => $pengajuan->id_perusahaan
                        ]);
                    }
                }
            }
        });
    }

    // Relasi ke model Perusahaan
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis_siswa', 'nis');
    }

    // Relasi ke model KepalaProgram
    public function kepalaProgram(): BelongsTo
    {
        return $this->belongsTo(KepalaProgram::class, 'nip_kepala_program', 'nip_kepala_program');
    }

    // Relasi ke model StaffHubin
    public function staffHubin(): BelongsTo
    {
        return $this->belongsTo(StaffHubin::class, 'nip_staff', 'nip_staff');
    }
}