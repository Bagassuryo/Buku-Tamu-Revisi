<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {

            // ganti nama layanan -> opd
            $table->renameColumn('layanan', 'opd');
        });

        // ubah tipe enum menjadi varchar/string
        DB::statement('ALTER TABLE guests MODIFY opd VARCHAR(255)');

        Schema::table('guests', function (Blueprint $table) {

            // buat kolom layanan baru
            $table->string('layanan')
                  ->nullable()
                  ->after('opd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {

            // hapus layanan baru
            $table->dropColumn('layanan');
        });

        Schema::table('guests', function (Blueprint $table) {

            // kembalikan opd -> layanan
            $table->renameColumn('opd', 'layanan');
        });
    }
};