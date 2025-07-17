<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| Model: KepalaSekolah
|--------------------------------------------------------------------------
*/
class KepalaSekolah extends Model
{
    use HasFactory;
    protected $table = 'kepala_sekolah';
    protected $primaryKey = 'id_kepsek';
    public $timestamps = false;
    protected $fillable = ['nama_kepala_sekolah', 'jabatan', 'nip_kepsek'];

    public function monitoring(): HasMany
    {
        return $this->hasMany(Monitoring::class, 'id_kepsek', 'id_kepsek');
    }
}