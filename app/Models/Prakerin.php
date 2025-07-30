<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/*
|--------------------------------------------------------------------------
| Model: Prakerin
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `prakerin`.
| Berisi data siswa yang sedang atau telah melaksanakan prakerin.
|
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prakerin extends Model
{
    use HasFactory;

    protected $table = 'prakerin';
    protected $primaryKey = 'id_prakerin';
    public $timestamps = false; // Menonaktifkan timestamps

    protected $fillable = [
        'nis_siswa',
        'nip_pembimbing_sekolah',
        'id_pembimbing_perusahaan',
        'id_perusahaan',
        'nip_kepala_program',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status_prakerin', // baru: aktif, selesai, dibatalkan
    ];

    // Relasi ke model Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis_siswa', 'nis');
    }

    // Relasi ke model PembimbingSekolah
    public function pembimbingSekolah(): BelongsTo
    {
        return $this->belongsTo(PembimbingSekolah::class, 'nip_pembimbing_sekolah', 'nip_pembimbing_sekolah');
    }

    // Relasi ke model PembimbingPerusahaan
    public function pembimbingPerusahaan(): BelongsTo
    {
        return $this->belongsTo(PembimbingPerusahaan::class, 'id_pembimbing_perusahaan', 'id_pembimbing');
    }

    // Relasi ke model Perusahaan
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model KepalaProgram
    public function kepalaProgram(): BelongsTo
    {
        return $this->belongsTo(KepalaProgram::class, 'nip_kepala_program', 'nip_kepala_program');
    }
}