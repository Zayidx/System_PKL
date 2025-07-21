<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/*
|--------------------------------------------------------------------------
| Model: Guru
|--------------------------------------------------------------------------
*/
class Guru extends Model
{
    use HasFactory;
    protected $table = 'guru';
    protected $primaryKey = 'nip_guru';
    public $timestamps = false;
    protected $fillable = ['nama_guru','kontak_guru'];


    public function kepalaProgram(): HasOne
    {
        return $this->hasOne(KepalaProgram::class, 'nip_guru', 'nip_guru');
    }

}
