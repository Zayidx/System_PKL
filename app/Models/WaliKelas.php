<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/*
|--------------------------------------------------------------------------
| Model: WaliKelas
|--------------------------------------------------------------------------
*/
class WaliKelas extends Model
{
    use HasFactory;
    protected $table = 'wali_kelas';
    protected $primaryKey = 'nip_wali_kelas';
    public $timestamps = false;
    protected $fillable = ['user_id', 'nama_wali_kelas'];

    /**
     * Relasi ke model User (akun wali kelas)
     * @return BelongsTo<User, WaliKelas>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi ke model Kelas
     * @return HasOne<Kelas>
     */
    public function kelas(): HasOne
    {
        return $this->hasOne(Kelas::class, 'nip_wali_kelas', 'nip_wali_kelas');
    }
}
