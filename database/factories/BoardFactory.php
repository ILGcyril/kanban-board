<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Board>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'space_id' => Space::factory(), // Автоматически создает пространство (и юзера внутри него)
            'name' => fake()->word() . ' Board', // Например: "Development Board"
        ];
    }
    
    public function forSpace(Space $space): static
    {
        return $this->state(function (array $attributes) use ($space) {
            return [
                'space_id' => $space->id,
            ];
        });
    }
}
