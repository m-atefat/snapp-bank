<?php

namespace Database\Factories;

use App\Exceptions\InvalidCardNumberException;
use App\Exceptions\InvalidCardNumberLengthException;
use App\Models\Account;
use App\Models\User;
use App\ValueObjects\Card;
use Illuminate\Database\Eloquent\Factories\Factory;
use Throwable;

/**
 * @extends Factory<User>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws InvalidCardNumberException
     * @throws InvalidCardNumberLengthException
     * @throws Throwable
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'number' => Card::fromString($this->faker->unique()->creditCardNumber('Visa')),
        ];
    }
}
