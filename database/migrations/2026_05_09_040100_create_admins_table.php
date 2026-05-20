<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'super_admin'])->default('admin');
            $table->string('opd')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif'); // Kolom Status
            $table->timestamp('last_active')->nullable(); // Kolom Terakhir Aktif
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    /*
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
