<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'account_id','name','email','password','role','status','login_status','last_login_at','last_seen_at'
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'last_seen_at'  => 'datetime',
    ];

    // Tambahkan helper optional
    public function isOnline(int $minutes = 3): bool
    {
        if ($this->login_status !== 'online' || !$this->last_seen_at) return false;
        return $this->last_seen_at->gt(now()->subMinutes($minutes));
    }
}
