<?php

namespace Database\Factories;

use App\Exceptions\InvalidAmountException;
use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Throwable;

/**
 * @extends Factory<User>
 */
class FeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws InvalidAmountException
     * @throws Throwable
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'amount' => Amount::forge($this->faker->biasedNumberBetween(100000, 99999999)),
        ];
    }
}
