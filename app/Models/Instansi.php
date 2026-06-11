<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instansi extends Model
{

    use SoftDeletes;

    protected $table = 'instansi';
    protected $fillable = ['nama', 'desc'];

    // Relasi ke Layanan
    public function layanan()
    {
        return $this->hasMany(\App\Models\Layanan::class, 'instansi_id');
    }

    // Relasi ke Tamu/Guest
    public function tamu()
    {
        return $this->hasMany(\App\Models\Guest::class, 'instansi_id');
    }
}
