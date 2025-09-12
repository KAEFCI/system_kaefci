<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryKaryawan extends Model
{
    use HasFactory;

    protected $table = 'history_karyawans';

    protected $fillable = [
        'nama_karyawan',
        'jabatan',
        'tanggal_masuk',
        'tanggal_keluar',
        'status_kepegawaian',
        'is_bermasalah',
        'catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'is_bermasalah' => 'boolean',
    ];
}
