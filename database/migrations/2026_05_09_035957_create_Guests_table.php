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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tamu');
            $table->foreignId('instansi_id')->nullable();
            $table->foreignId('layanan_id')->nullable();
            $table->string('no_hp');
            $table->string('asal_instansi');
            $table->text('keterangan');
            $table->date('tanggal');
            $table->time('datang');
            $table->time('pulang')->nullable();
            $table->string('foto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
