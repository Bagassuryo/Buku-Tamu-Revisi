<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk proses login
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
        'instansi_id',
        'last_active',
    ];

    protected $casts = [
        'last_active' => 'datetime',
    ];
}
