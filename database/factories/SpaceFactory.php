<?php

namespace Database\Factories;

use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Space>
 */
class SpaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Автоматически создает пользователя
            'name' => fake()->sentence(3), // Например: "Мой новый проект"
            'description' => fake()->optional()->paragraph(), // Описание может быть пустым (nullable)
        ];
    }
}
