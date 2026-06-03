<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = ['instansi_id', 'nama_layanan', 'urutan'];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }
}
