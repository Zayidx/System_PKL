<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * Menentukan bahwa model ini tidak menggunakan kolom timestamp (created_at & updated_at).
     * Ini penting karena tabel 'roles' di migrasi Anda tidak memilikinya.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     * Ini adalah daftar kolom yang boleh diisi saat membuat atau mengupdate data
     * menggunakan metode seperti Role::create([...]).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'keterangan',
    ];

    /**
     * Mendefinisikan relasi "hasMany" (satu Role memiliki banyak User).
     *
     * Fungsi ini akan mengembalikan semua user yang memiliki role ini.
     * Nama fungsi 'users' (plural) adalah konvensi untuk relasi hasMany.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        // Parameter kedua ('roles_id') adalah nama foreign key di tabel 'users'.
        // Kita perlu menentukannya secara eksplisit di sini.
        return $this->hasMany(User::class, 'roles_id');
    }
}
