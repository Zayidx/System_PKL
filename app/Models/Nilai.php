<?php

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
}