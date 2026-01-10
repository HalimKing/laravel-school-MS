<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'first_name' => fake()->name(),
            'last_name' => fake()->name(),
            'middle_name' => fake()->name(),
            'other_name' => fake()->name(),
            'student_id' => fake()->unique()->randomNumber(6),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(),
            'address' => fake()->address(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'parent_name' => fake()->name(),
            'parent_email' => fake()->unique()->safeEmail(),
            'parent_phone' => fake()->phoneNumber(),
        ];
    }
}
