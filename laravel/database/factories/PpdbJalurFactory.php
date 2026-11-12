<?php

namespace Database\Factories;

use App\Models\PpdbJalur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PpdbJalur>
 */
class PpdbJalurFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Jalur Reguler',
            'kode' => fake()->unique()->slug(2),
            'deskripsi' => 'Pendaftaran siswa baru jalur reguler SMP PGRI 4 Waru.',
            'persyaratan' => 'Lulusan SD/MI sederajat, menyerahkan KK, Akta Kelahiran, SKL, dan Pas Foto.',
            'tanggal_mulai' => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->addMonths(4)->toDateString(),
            'biaya_daftar' => 0,
            'kuota' => 100,
            'dibuka' => true,
            'urutan' => 1,
        ];
    }
}
