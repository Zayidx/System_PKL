<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: KontakGuru
|--------------------------------------------------------------------------
*/
class KontakGuru extends Model
{
    use HasFactory;
    protected $table = 'kontak_guru';
    public $timestamps = false;
    protected $fillable = ['id_guru', 'kontak'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'nip_guru');
    }
}