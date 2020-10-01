<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'appoint_id' => rand(2, 11),
            'creator_id' => 1,
            'status_id' => 2,
            'name' => $this->faker->name,
            'description' => $this->faker->text
        ];
    }
}
