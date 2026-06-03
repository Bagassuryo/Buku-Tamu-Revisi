<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    protected $table = 'instansi';
    protected $fillable = ['nama', 'desc'];

    public function layanan()
    {
        return $this->hasMany(Layanan::class);
    }
}
