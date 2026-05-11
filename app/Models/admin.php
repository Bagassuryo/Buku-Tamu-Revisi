<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk proses login
use Illuminate\Notifications\Notifiable;

class admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
        'last_active',
    ];

    protected $casts = [
        'last_active' => 'datetime',
    ];
}
