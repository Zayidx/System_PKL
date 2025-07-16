<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'foto',
        'roles_id', // Foreign key harus dimasukkan ke fillable agar bisa diisi saat create user
    ];

    /**
     * Atribut yang harus disembunyikan saat model diubah menjadi array atau JSON.
     * Ini adalah langkah keamanan untuk mencegah data sensitif seperti password terekspos.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     * Di sini, kita memastikan bahwa setiap kali kita set atribut 'password',
     * Laravel akan secara otomatis melakukan hashing.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" (satu User milik satu Role).
     *
     * Fungsi ini akan mengembalikan role dari user yang bersangkutan.
     * Nama fungsi 'role' (singular) adalah konvensi untuk relasi belongsTo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        // Parameter kedua ('roles_id') adalah nama foreign key di tabel 'users' ini.
        // Parameter ketiga ('id') adalah primary key di tabel 'roles'.
        return $this->belongsTo(Role::class, 'roles_id', 'id');
    }
}
