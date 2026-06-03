<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'nama_tamu',
        'instansi_id',  
        'layanan_id',   
        'no_hp',
        'asal_instansi',
        'keterangan',
        'tanggal',
        'datang',
        'pulang',
        'foto'
    ];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}