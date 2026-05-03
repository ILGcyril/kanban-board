<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\Space;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board_id' => Board::factory(), // Автоматически создает доску (и пространство, и юзера)
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['todo', 'in_progress', 'done']),
            'order_column' => 0,
        ];
    }

    public function forBoard(Board $board): static
    {
        return $this->state(function (array $attributes) use ($board) {
            return [
                'board_id' => $board->id,
            ];
        });
    }
}
