<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Ditambahkan untuk relasi
        'nama_kepala_sekolah',
        'jabatan',
        'nip_kepsek'
    ];

    /**
     * Relasi ke model Monitoring
     * @return HasMany<Monitoring>
     */
    public function monitoring(): HasMany
    {
        return $this->hasMany(Monitoring::class, 'id_kepsek', 'id_kepsek');
    }

    /**
     * Relasi ke model User (akun kepala sekolah)
     * @return BelongsTo<User, KepalaSekolah>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
