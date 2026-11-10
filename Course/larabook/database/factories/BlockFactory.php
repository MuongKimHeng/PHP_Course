<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic_id' => Topic::factory(),
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(),
            'image_path' => null,
        ];
    }
}
