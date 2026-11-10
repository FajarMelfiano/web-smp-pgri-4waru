<?php

namespace Database\Factories;

use App\Models\Pendaftar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pendaftar>
 */
class PendaftarFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_siswa' => fake()->name(),
            'nama_wali' => fake()->name(),
            'asal_sd' => 'SDN Kureksari',
            'whatsapp' => '081234567890',
            'catatan' => 'Pertanyaan jadwal pendaftaran.',
            'status' => 'baru',
            'sumber' => 'web',
            'ip' => '127.0.0.1',
        ];
    }
}
