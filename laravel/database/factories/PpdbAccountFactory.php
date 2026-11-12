<?php

namespace Database\Factories;

use App\Models\PpdbAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<PpdbAccount>
 */
class PpdbAccountFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'nik' => fake()->unique()->numerify('3515############'),
            'tanggal_lahir' => '2014-05-12',
            'whatsapp' => '62812'.fake()->numerify('########'),
            'password' => static::$password ??= Hash::make('Rahasia123'),
            'remember_token' => Str::random(10),
        ];
    }
}
