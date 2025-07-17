<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| Model: Jurusan
|--------------------------------------------------------------------------
*/
class Jurusan extends Model
{
    use HasFactory;
    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';
    public $timestamps = false;
    protected $fillable = ['nama_jurusan_lengkap', 'nama_jurusan_singkat', 'kepala_program'];

    public function kepalaProgram(): BelongsTo
    {
        return $this->belongsTo(KepalaProgram::class, 'kepala_program', 'nip_kepala_program');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'id_jurusan', 'id_jurusan');
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'id_jurusan', 'id_jurusan');
    }

    public function kompetensi(): HasMany
    {
        return $this->hasMany(Kompetensi::class, 'id_jurusan', 'id_jurusan');
    }
}