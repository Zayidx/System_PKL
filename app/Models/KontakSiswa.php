<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: KontakSiswa
|--------------------------------------------------------------------------
*/
class KontakSiswa extends Model
{
    use HasFactory;
    protected $table = 'kontak_siswa';
    public $timestamps = false;
    protected $fillable = ['nis_siswa', 'kontak'];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis_siswa', 'nis');
    }
}