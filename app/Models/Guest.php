<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['nama_tamu', 'opd', 'layanan', 'no_hp', 'asal_instansi', 'keterangan', 'tanggal', 'datang', 'pulang', 'foto'];
}