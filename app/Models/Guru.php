<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $primaryKey = 'nip_guru';
    public $timestamps = false;
    
    // BARU: 'user_id' ditambahkan ke fillable
    protected $fillable = ['nip_guru', 'user_id', 'nama_guru', 'kontak_guru'];

    // BARU: Definisikan relasi bahwa satu Guru 'dimiliki oleh' satu User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kepalaProgram(): HasOne
    {
        return $this->hasOne(KepalaProgram::class, 'nip_guru', 'nip_guru');
    }
}