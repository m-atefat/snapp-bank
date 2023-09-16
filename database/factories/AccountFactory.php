<?php

namespace Database\Factories;

use App\Models\User;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'number' => $this->faker->unique()->numerify('############'),
            'balance' => Amount::forge(100000000)
        ];
    }
}
