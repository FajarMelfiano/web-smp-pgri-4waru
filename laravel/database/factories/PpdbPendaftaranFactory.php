<?php

namespace Database\Factories;

use App\Models\PpdbAccount;
use App\Models\PpdbPendaftaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PpdbPendaftaran>
 */
class PpdbPendaftaranFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_pendaftaran' => 'PPDB-2026-'.fake()->unique()->numerify('####'),
            'ppdb_account_id' => PpdbAccount::factory(),
            'ppdb_jalur_id' => null,
            'tahun_ajaran' => '2026/2027',
            'status_seleksi' => 'proses',
        ];
    }
}
