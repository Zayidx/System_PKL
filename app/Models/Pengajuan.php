<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/*
|--------------------------------------------------------------------------
| Model: Pengajuan
|--------------------------------------------------------------------------
|
| Model ini merepresentasikan tabel `pengajuan`.
| Berisi data pengajuan prakerin yang dilakukan oleh siswa.
|
*/
class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';
    public $timestamps = true; // Mengaktifkan timestamps created_at dan updated_at

    protected $fillable = [
        'nis_siswa',
        'id_perusahaan',
        'nip_kepala_program',
        'nip_staff',
        'status_pengajuan',
        'bukti_penerimaan',
        'tanggal_mulai', // kontrak PKL
        'tanggal_selesai', // kontrak PKL
        'link_cv', // link CV siswa
    ];

    // Relasi ke model Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nis_siswa', 'nis');
    }

    // Relasi ke model Perusahaan
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class, 'id_perusahaan', 'id_perusahaan');
    }

    // Relasi ke model KepalaProgram
    public function kepalaProgram(): BelongsTo
    {
        return $this->belongsTo(KepalaProgram::class, 'nip_kepala_program', 'nip_kepala_program');
    }

    // Relasi ke model StaffHubin
    public function staffHubin(): BelongsTo
    {
        return $this->belongsTo(StaffHubin::class, 'nip_staff', 'nip_staff');
    }
}