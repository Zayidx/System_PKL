<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: PembimbingSekolah
|--------------------------------------------------------------------------
*/
class PembimbingSekolah extends Model
{
    use HasFactory;
    protected $table = 'pembimbing_sekolah';
    protected $primaryKey = 'nip_pembimbing_sekolah';
    public $timestamps = false;
    protected $fillable = ['user_id', 'nama_pembimbing_sekolah'];

    /**
     * Relasi ke model User (akun pembimbing sekolah)
     * @return BelongsTo<User, PembimbingSekolah>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}