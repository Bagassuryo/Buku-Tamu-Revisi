<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role',
        'status',
        'instansi_id',
        'last_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_active' => 'datetime',
    ];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }
}
