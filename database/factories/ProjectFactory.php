<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plobin\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Plobin\Project::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['planning', 'in_progress', 'completed', 'pending']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'progress' => fake()->numberBetween(0, 100),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'team' => fake()->randomElements(['개발자', '디자이너', 'PM', 'QA'], fake()->numberBetween(1, 4))
        ];
    }
}
