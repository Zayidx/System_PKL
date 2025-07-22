<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/*
|--------------------------------------------------------------------------
| Model: StaffHubin
|--------------------------------------------------------------------------
*/
class StaffHubin extends Model
{
    use HasFactory;
    protected $table = 'staff_hubin';
    protected $primaryKey = 'nip_staff';
    public $timestamps = false;
    protected $fillable = ['user_id', 'nama_staff'];

    /**
     * Relasi ke model User (akun staff hubin)
     * @return BelongsTo<User, StaffHubin>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}