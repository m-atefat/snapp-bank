<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\InvalidAmountException;
use App\Models\Card;
use App\Models\User;
use App\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Throwable;

/**
 * @extends Factory<User>
 */
class TransactionFactory extends Factory
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
        $failed = $this->faker->boolean(90);

        return [
            'card_id' => Card::factory(),
            'type' => $this->faker->randomElement(TransactionType::values()),
            'status' => $failed ? TransactionStatus::FAILED : TransactionStatus::DONE,
            'amount' => Amount::forge($this->faker->biasedNumberBetween(100000, 99999999)),
            'track_id' => $failed ? null : Str::uuid()->toString(),
            'done_at' => $failed ? null : now(),
        ];
    }
}
