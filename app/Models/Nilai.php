<?php

namespace App\Models;

/*
|--------------------------------------------------------------------------
| Model: Nilai (Pivot Model)
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel pivot `nilai`.
| Digunakan dalam relasi BelongsToMany antara Penilaian dan Kompetensi.
|
*/

use Illuminate\Database\Eloquent\Relations\Pivot;

class Nilai extends Pivot
{
    protected $table = 'nilai';
    
    protected $fillable = [
        'id_penilaian',
        'id_kompetensi', 
        'nilai'
    ];
    
    public $timestamps = false;
}