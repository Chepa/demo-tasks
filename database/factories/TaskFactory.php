<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->text(),
            'text' => $this->faker->text(),
            'completed' => $this->faker->boolean(20),
        ];
    }
}
