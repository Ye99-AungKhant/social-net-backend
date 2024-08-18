<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friendship>
 */
class FriendshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'adding_user_id' => $this->faker->numberBetween(1, 20),
            'added_user_id' => $this->faker->numberBetween(30, 50),
            'status' => 'Accepted'
        ];
    }
}
