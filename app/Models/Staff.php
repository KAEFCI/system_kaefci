<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    protected $table = 'staff'; // konek ke tabel staff

    protected $fillable = [
        'id',
        'name',
        'email',
        'role',
        'status',
        'password',
        'team_id'
    ];

    protected $hidden = [
        'password',
    ];

    // Primary key adalah string (contoh: ACC01), bukan auto-increment
    public $incrementing = false;
    protected $keyType = 'string';
    // default: $incrementing = true (auto-increment) dan $keyType = 'int'

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
