<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembimbingPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'pembimbing_perusahaan';
    protected $primaryKey = 'id_pembimbing';
    public $timestamps = false;

    // BARU: 'user_id' ditambahkan ke fillable
    protected $fillable = [
        'id_perusahaan',
        'user_id',
        'nama',
        'no_hp',
        'email',
    ];

    /**
     * Relasi ke model User (akun pembimbing perusahaan)
     * @return BelongsTo<User, PembimbingPerusahaan>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Perusahaan
     * @return HasMany<Perusahaan>
     */
    public function perusahaan(): HasMany
    {
        return $this->hasMany(Perusahaan::class, 'id_pembimbing_perusahaan', 'id_pembimbing');
    }

    /**
     * Relasi ke model Prakerin
     * @return HasMany<Prakerin>
     */
    public function prakerin(): HasMany
    {
        return $this->hasMany(Prakerin::class, 'id_pembimbing_perusahaan', 'id_pembimbing');
    }
    
    /**
     * Relasi ke model Penilaian
     * @return HasMany<Penilaian>
     */
    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'id_pemb_perusahaan', 'id_pembimbing');
    }
}
