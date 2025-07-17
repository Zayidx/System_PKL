<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
/*
|--------------------------------------------------------------------------
| Model: Monitoring
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `monitoring`.
| Berisi catatan hasil monitoring dari pembimbing sekolah.
|
*/

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';
    protected $primaryKey = 'id_monitoring';
    public $timestamps = false;

    protected $fillable = [
        'id_perusahaan',
        'nip_pembimbing_sekolah',
        'id_kepsek',
        'tanggal',
        'catatan',
        'verifikasi',
    ];

    // Relasi ke model Perusahaan
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model PembimbingSekolah
    public function pembimbingSekolah(): BelongsTo
    {
        return $this->belongsTo(PembimbingSekolah::class, 'nip_pembimbing_sekolah', 'nip_pembimbing_sekolah');
    }

    // Relasi ke model KepalaSekolah
    public function kepalaSekolah(): BelongsTo
    {
        return $this->belongsTo(KepalaSekolah::class, 'id_kepsek', 'id_kepsek');
    }
}