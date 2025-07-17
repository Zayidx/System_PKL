<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| Model: Siswa
|--------------------------------------------------------------------------
*/
class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    public $incrementing = false; // Karena primary key bukan auto-increment
    protected $keyType = 'string'; // Tipe data primary key adalah string
    public $timestamps = false;
    protected $fillable = ['nis', 'user_id', 'id_kelas', 'id_jurusan', 'nama_siswa', 'tempat_lahir', 'tanggal_lahir', 'id_perusahaan'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    public function kontak(): HasMany
    {
        return $this->hasMany(KontakSiswa::class, 'nis_siswa', 'nis');
    }
    
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'nis_siswa', 'nis');
    }
}
