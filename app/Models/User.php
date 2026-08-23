<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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

    public function branch()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function hasActiveDoubleShiftPermit(?Carbon $now = null): bool
    {
        $now = $now ?? Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();

        $isValidDate = $this->double_shift_date === $today
            || ($this->shift_id == 3 && $this->double_shift_date === $yesterday);

        return (bool) $this->can_double_shift && $isValidDate;
    }
}
