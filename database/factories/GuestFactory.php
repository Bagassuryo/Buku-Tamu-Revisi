<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    public function definition(): array
    {
        // Data layanan berdasarkan OPD
        $opdLayanan = [

            'Dinas Komunikasi dan Informatika' => [
                'Bidang SIP',
                'Bidang SPBE',
                'Bidang TI',
                'Kepala Dinas Kominfo',
                'Radio',
                'Sekretariat',
                'Sekretariat Dinas Kominfo'
            ],

            'Dinas Pendidikan' => [
                'Pendidikan Anak Usia Dini (PAUD)',
                'Pendidikan Dasar (SD/SMP)',
                'Pendidikan Non Formal',
                'Penerimaan Peserta Didik Baru (PPDB)',
                'Data Pokok Pendidikan (Dapodik)',
                'Izin Operasional Sekolah'
            ],

            'Dinas Kesehatan' => [
                'Pelayanan Kesehatan Dasar',
                'Izin Sarana Kesehatan',
                'Surveilans dan Imunisasi',
                'Kesehatan Ibu dan Anak (KIA)',
                'Promosi Kesehatan'
            ],

            'Dinas Perhubungan' => [
                'Pengujian Kendaraan Bermotor (KIR)',
                'Izin Trayek Angkutan',
                'Manajemen Lalu Lintas',
                'Perparkiran',
                'Izin Pelabuhan / Terminal'
            ],
        ];

        // Pilih OPD random
        $opd = $this->faker->randomElement(array_keys($opdLayanan));

        return [
            'nama_tamu' => $this->faker->name(),

            // OPD random
            'opd' => $opd,

            // Layanan mengikuti OPD
            'layanan' => $this->faker->randomElement($opdLayanan[$opd]),

            'no_hp' => $this->faker->numerify('08##########'),
            'asal_instansi' => $this->faker->company(),
            'keterangan' => $this->faker->sentence(),
            'tanggal' => now()->format('Y-m-d'),
            'datang' => now()->format('H:i:s'),
            'pulang' => null,
            'foto' => null,
        ];
    }
}
