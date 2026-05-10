<?php

namespace Database\Factories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_tamu' => $this->faker->name(),
            'layanan' => $this->faker->randomElement(['bidang sip', 'bidang spbe', 'bidang ti', 'kepala dinas kominfo', 'radio', 'sekretariat', 'sekretariat dinas kominfo']),
            'no_hp' => $this->faker->numerify('08##########'),
            'asal_instansi' => $this->faker->company(),
            'keterangan' => $this->faker->sentence(),
            'tanggal' => now()->format('Y-m-d'),
            'datang' => now()->format('H:i:s'),
            'pulang' => null, // Sesuai permintaanmu, awal diset null
            'foto' => null,
        ];
    }
}
