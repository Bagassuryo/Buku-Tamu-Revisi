<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // 1. Kolom Role (jika sebelumnya belum ada)
            // $table->string('role')->default('admin')->after('password');

            // 2. Kolom Status (Aktif/Nonaktif)
            if (!Schema::hasColumn('admins', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('role');
            }

            // 3. Kolom Last Active (Terakhir Login)
            if (!Schema::hasColumn('admins', 'last_active')) {
                $table->timestamp('last_active')->nullable()->after('status');
            }

            // 4. Kolom Timestamps (created_at & updated_at)
            // Laravel akan otomatis mengisi ini karena kamu sudah menghapus public $timestamps = false
            if (!Schema::hasColumn('admins', 'created_at')) {
                $table->timestamps(); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['status', 'last_active', 'created_at', 'updated_at']);
        });
    }
};