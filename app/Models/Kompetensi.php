<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/*
|--------------------------------------------------------------------------
| Model: Kompetensi
|--------------------------------------------------------------------------
*/
class Kompetensi extends Model
{
    use HasFactory;
    protected $table = 'kompetensi';
    protected $primaryKey = 'id_kompetensi';
    public $timestamps = false;
    protected $fillable = ['id_jurusan', 'nama_kompetensi'];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function penilaian(): BelongsToMany
    {
        return $this->belongsToMany(Penilaian::class, 'nilai', 'id_kompetensi', 'id_penilaian')
                    ->withPivot('nilai');
    }
}
