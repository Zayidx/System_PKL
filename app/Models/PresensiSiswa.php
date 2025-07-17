<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/*
|--------------------------------------------------------------------------
| Model: PresensiSiswa
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `presensi_siswa`.
| Berisi data kehadiran harian siswa selama prakerin.
|
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PresensiSiswa extends Model
{
    use HasFactory;

    protected $table = 'presensi_siswa';
    protected $primaryKey = 'id_presensi';
    public $timestamps = false;

    protected $fillable = [
        'id_pembimbing_perusahaan',
        'tanggal_kehadiran',
        'jam_masuk',
        'jam_pulang',
        'kegiatan',
        'keterangan',
        'status',
    ];

    // Relasi ke model PembimbingPerusahaan
    public function pembimbingPerusahaan(): BelongsTo
    {
        return $this->belongsTo(PembimbingPerusahaan::class, 'id_pembimbing_perusahaan', 'id_pembimbing');
    }
}