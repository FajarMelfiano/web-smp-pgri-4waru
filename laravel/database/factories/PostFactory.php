<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'judul' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'ringkasan' => fake()->sentence(12),
            'isi' => fake()->paragraphs(3, true),
            'status' => 'terbit',
            'published_at' => now(),
        ];
    }

    public function draf(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draf',
            'published_at' => null,
        ]);
    }
}
