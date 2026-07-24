<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'hotel_id',
        'shift_id',
        'name',
        'email',
        'password',
        'role',
        'department',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // NAMA RELASI DIUBAH JADI branch AGAR TIDAK BENTROK DENGAN KOLOM hotel
    public function branch()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
