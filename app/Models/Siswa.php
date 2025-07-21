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
| Model ini merepresentasikan tabel `siswa`.
*/
class Siswa extends Model
{
    use HasFactory;
    
    // Properti dasar model
    protected $table = 'siswa';
    protected $primaryKey = 'nis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * Array ini HARUS sesuai dengan kolom yang ada di tabel 'siswa'
     * pada file migrasi terakhir Anda.
     */
    protected $fillable = [
        'nis',
        'user_id',
        'id_kelas',
        'id_jurusan',
        'nama_siswa',
        'tempat_lahir',
        'tanggal_lahir',
        'kontak_siswa' // Kolom ini sekarang menjadi bagian dari tabel siswa
    ];

    /**
     * Mendefinisikan relasi ke model User.
     * Satu siswa memiliki satu akun user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Mendefinisikan relasi ke model Kelas.
     * Satu siswa berada di satu kelas.
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    /**
     * Mendefinisikan relasi ke model Jurusan.
     * Satu siswa memiliki satu jurusan.
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    /**
     * Mendefinisikan relasi ke model Perusahaan (jika siswa sudah punya tempat PKL).
     * Ini mungkin memerlukan kolom 'id_perusahaan' di tabel siswa.
     * Jika tidak ada, relasi ini tidak akan berfungsi.
     */
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }
    
    /**
     * Mendefinisikan relasi ke model Pengajuan.
     * Satu siswa bisa memiliki banyak pengajuan.
     */
    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'nis_siswa', 'nis');
    }
}
