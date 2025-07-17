<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: KontakPerusahaan
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `kontak_perusahaan`.
| Berisi data kontak (nomor telepon, dll) dari perusahaan.
|
*/
class KontakPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'kontak_perusahaan';
    protected $primaryKey = 'id_kontak';
    public $timestamps = false;

    protected $fillable = [
        'id_perusahaan',
        'kontak_perusahaan',
    ];

    // Relasi ke model Perusahaan
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }
}