<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
        'username' => 'admin',
        'password' => \Illuminate\Support\Facades\Hash::make('admin'),
        'role' => 'admin'
    ]);

    \App\Models\Admin::create([
        'username' => 'superadmin',
        'password' => \Illuminate\Support\Facades\Hash::make('admin'),
        'role' => 'super_admin'
    ]);
    }
}
