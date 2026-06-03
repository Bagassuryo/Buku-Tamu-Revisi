<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (Schema::hasColumn('guests', 'instansi_id')) {
                $table->foreign('instansi_id')
                    ->references('id')
                    ->on('instansi')
                    ->onDelete('set null');
            }

            if (Schema::hasColumn('guests', 'layanan_id')) {
                $table->foreign('layanan_id')
                    ->references('id')
                    ->on('layanan')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            if (Schema::hasColumn('guests', 'layanan_id')) {
                $table->dropForeign(['layanan_id']);
            }
            if (Schema::hasColumn('guests', 'instansi_id')) {
                $table->dropForeign(['instansi_id']);
            }
        });
    }
};
