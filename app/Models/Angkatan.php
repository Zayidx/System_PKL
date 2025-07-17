<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/*
|--------------------------------------------------------------------------
| Model: Angkatan
|--------------------------------------------------------------------------
*/
class Angkatan extends Model
{
    use HasFactory;
    protected $table = 'angkatan';
    protected $primaryKey = 'id_angkatan';
    public $timestamps = false;
    protected $fillable = ['tahun'];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'id_angkatan', 'id_angkatan');
    }
}