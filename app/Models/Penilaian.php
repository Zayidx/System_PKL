<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Model: Penilaian
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `penilaian`.
| Tabel induk yang menghubungkan siswa dengan nilai dan sertifikat.
|
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Nilai;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';
    protected $primaryKey = 'id_penilaian';
    public $timestamps = false;

    protected $fillable = [
        'nis_siswa',
        'id_pemb_perusahaan',
    ];

    // Relasi ke model Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis_siswa', 'nis');
    }

    // Relasi ke model PembimbingPerusahaan
    public function pembimbingPerusahaan(): BelongsTo
    {
        return $this->belongsTo(PembimbingPerusahaan::class, 'id_pemb_perusahaan', 'id_pembimbing');
    }

    // Relasi ke Sertifikat
    public function sertifikat(): HasOne
    {
        return $this->hasOne(Sertifikat::class, 'id_penilaian', 'id_penilaian');
    }

    // Relasi Many-to-Many ke Kompetensi melalui tabel pivot `nilai`
    public function kompetensi(): BelongsToMany
    {
        return $this->belongsToMany(Kompetensi::class, 'nilai', 'id_penilaian', 'id_kompetensi')
                    ->withPivot('nilai') // Mengambil kolom `nilai` dari tabel pivot
                    ->using(Nilai::class); // Menggunakan model pivot kustom
    }
}