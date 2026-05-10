<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Penting untuk proses login
use Illuminate\Notifications\Notifiable;

class admin extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
    'username',
    'password',
    'role',
];
}
