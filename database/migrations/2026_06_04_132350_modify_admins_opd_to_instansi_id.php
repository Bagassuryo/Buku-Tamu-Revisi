<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admins') && Schema::hasColumn('admins', 'instansi_id')) {
            Schema::table('admins', function (Blueprint $table) {
                // Kolom instansi_id sudah ada dari migration sebelumnya.
            });
        }
    }

    public function down(): void
    {
        //
    }
};
