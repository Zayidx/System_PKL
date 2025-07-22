<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: KepalaProgram
|--------------------------------------------------------------------------
*/
class KepalaProgram extends Model
{
    use HasFactory;
    protected $table = 'kepala_program';
    protected $primaryKey = 'nip_kepala_program';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',      // Ditambahkan untuk relasi
        'id_jurusan',
        'nama_kepala_program'
    ];
    
    /**
     * Relasi ke model User (akun kepala program)
     * @return BelongsTo<User, KepalaProgram>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Jurusan
     * @return BelongsTo<Jurusan, KepalaProgram>
     */
    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }
}
