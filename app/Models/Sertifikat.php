<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: Sertifikat
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `sertifikat`.
| Berisi path atau nama file sertifikat yang didapat siswa.
|
*/
class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikat';
    protected $primaryKey = 'id_sertifikat';
    public $timestamps = false;

    protected $fillable = [
        'id_penilaian',
        'file_sertifikat',
    ];

    // Relasi ke model Penilaian
    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class, 'id_penilaian', 'id_penilaian');
    }
}
