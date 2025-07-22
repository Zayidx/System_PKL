<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'foto',
        'roles_id', // Foreign key harus ada di fillable
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Mendefinisikan relasi "belongsTo" ke model Role.
     * Satu User pasti memiliki satu Role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'roles_id', 'id');
    }

    /**
     * BARU: Mendefinisikan relasi "hasOne" ke model Siswa.
     * Ini berarti satu User berelasi dengan satu data Siswa.
     * Sangat berguna untuk memanggil data siswa, contoh: auth()->user()->siswa->nis
     */
    public function siswa(): HasOne
    {
        // Parameter kedua ('user_id') adalah foreign key di tabel 'siswa'.
        // Parameter ketiga ('id') adalah primary key di tabel 'users' ini.
        return $this->hasOne(Siswa::class, 'user_id', 'id');
    }
     public function guru(): HasOne
    {
        return $this->hasOne(Guru::class, 'user_id', 'id');
    }
     public function pembimbingPerusahaan(): HasOne
    {
        return $this->hasOne(PembimbingPerusahaan::class, 'user_id', 'id');
    }
     public function pembimbingSekolah(): HasOne
    {
        return $this->hasOne(PembimbingSekolah::class, 'user_id', 'id');
    }
      public function staffHubin(): HasOne
    {
        return $this->hasOne(StaffHubin::class, 'user_id', 'id');
    }
      public function kepalaSekolah(): HasOne
    {
        return $this->hasOne(KepalaSekolah::class, 'user_id', 'id');
    }

    public function kepalaProgram(): HasOne
    {
        return $this->hasOne(KepalaProgram::class, 'user_id', 'id');
    }
}
