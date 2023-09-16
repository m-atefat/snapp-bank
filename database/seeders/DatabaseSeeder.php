<?php

namespace Database\Seeders;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\Models\User;
use App\ValueObjects\Amount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $cardToCardFee = Amount::forge(config('fee.card_to_card'));

        User::factory(1)->create()
            ->each(fn(User $user) => Account::factory(2)
                ->create(['user_id' => $user->id])
                ->each(fn(Account $account) => Card::factory(2)
                    ->create(['account_id' => $account->id])
                    ->each(fn(Card $card) => Transaction::factory(50)
                        ->create([
                            'card_id' => $card->id,
                            'type' => TransactionType::CARD_TO_CARD,
                            'status' => TransactionStatus::DONE,
                            'track_id' => Str::uuid()->toString(),
                            'done_at' => now()->subMinutes(random_int(1, 20)),
                        ])->each(fn(Transaction $transaction) => Transaction::factory(1)
                            ->create([
                                'card_id' => Card::factory()->create(),
                                'amount' => $transaction->amount->decrease($cardToCardFee),
                                'is_deposit' => true,
                                'source_transaction_id' => $transaction->id,
                                'type' => TransactionType::CARD_TO_CARD,
                                'status' => TransactionStatus::DONE,
                                'track_id' => Str::uuid()->toString(),
                                'done_at' => now()->subMinutes(random_int(1, 20)),
                            ])
                        )->each(fn(Transaction $transaction) => Fee::factory(1)
                            ->create([
                                'transaction_id' => $transaction->id,
                                'amount' => $cardToCardFee
                            ])
                        )
                    )
                )
            );
    }
}
