<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| Model: PembimbingPerusahaan
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `pembimbing_perusahaan`.
| Berisi data pembimbing yang berasal dari pihak perusahaan.
|
*/
class PembimbingPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'pembimbing_perusahaan';
    protected $primaryKey = 'id_pembimbing';
    public $timestamps = false;

    protected $fillable = [
        'id_perusahaan',
        'nama',
        'no_hp',
    ];

    // Relasi ke model Perusahaan
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model Prakerin
    public function prakerin(): HasMany
    {
        return $this->hasMany(Prakerin::class, 'id_pembimbing_perusahaan', 'id_pembimbing');
    }
    
    // Relasi ke model Penilaian
    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'id_pemb_perusahaan', 'id_pembimbing');
    }
}