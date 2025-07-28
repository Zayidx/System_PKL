<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MitraPerusahaanPending extends Model
{
    protected $fillable = [
        'nama_perusahaan',
        'alamat_perusahaan',
        'email_perusahaan',
        'kontak_perusahaan',
        'status',
        'nis_pengaju',
        'catatan_staff',
    ];
}
