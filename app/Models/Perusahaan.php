<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| Model: Perusahaan
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `perusahaan`.
| Berisi data perusahaan tempat siswa melakukan prakerin.
|
*/
class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';
    protected $primaryKey = 'id_perusahaan';
    public $timestamps = false;

    protected $fillable = [
        'nama_perusahaan',
        'alamat_perusahaan',
        'email_perusahaan',
        'logo_perusahaan',
        'kontak_perusahaan', 
    ];

    // Relasi ke model PembimbingPerusahaan
    public function pembimbingPerusahaan(): HasMany
    {
        return $this->hasMany(PembimbingPerusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model KontakPerusahaan
    public function kontakPerusahaan(): HasMany
    {
        return $this->hasMany(KontakPerusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model Siswa
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'id_perusahaan', 'id_perusahaan');
    }
    
    // Relasi ke model Pengajuan
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_perusahaan', 'id_perusahaan');
    }
}