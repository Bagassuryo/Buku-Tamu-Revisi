<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $table = 'instansi';
    protected $fillable = ['kode', 'nama', 'desc', 'is_active'];

    public function layanan()
    {
        return $this->hasMany(Layanan::class);
    }
}
