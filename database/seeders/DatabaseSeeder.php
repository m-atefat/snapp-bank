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
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @throws Exception|Throwable
     */
    public function run(): void
    {
        $cardToCardFee = Amount::forge(config('fee.card_to_card'));

        $destinationCard1 = Card::factory()->create();
        $destinationCard2 = Card::factory()->create();
        $destinationCard3 = Card::factory()->create();
        $destinationCard4 = Card::factory()->create();
        $destinationCard5 = Card::factory()->create();

        $destinationCards = [
            $destinationCard1,
            $destinationCard2,
            $destinationCard3,
            $destinationCard4,
            $destinationCard5,
        ];

        User::factory(5)->create()
            ->each(fn(User $user) => Account::factory(2)
                ->create(['user_id' => $user->id, 'balance' => Amount::forge(100000000)])
                ->each(fn(Account $account) => Card::factory(2)
                    ->create(['account_id' => $account->id])
                    ->each(fn(Card $card) => Transaction::factory(100)->create([
                        'card_id'  => $card->id,
                        'type'     => TransactionType::CARD_TO_CARD,
                        'amount'   => $amount = Amount::forge(random_int(10000, 20000)),
                        'balance'  => $card->account->refresh()->balance->decrease($amount),
                        'status'   => TransactionStatus::DONE,
                        'track_id' => Str::uuid()->toString(),
                        'done_at'  => now()->subMinutes(random_int(1, 20)),
                    ])
                        ->each(fn(Transaction $transaction) => $card->account()->update([
                            'balance' => $card->account->refresh()->balance->decrease($transaction->amount)->getAmount()
                        ]))
                        ->each(fn(Transaction $transaction) => Transaction::factory(1)->create([
                            'card_id'               => $destinationCard = $destinationCards[random_int(0, 4)],
                            'amount'                => $transaction->amount->decrease($cardToCardFee),
                            'balance'               => $destinationCard->account->refresh()->balance->increase($transaction->amount->decrease($cardToCardFee)),
                            'is_deposit'            => true,
                            'source_transaction_id' => $transaction->id,
                            'type'                  => TransactionType::CARD_TO_CARD,
                            'status'                => TransactionStatus::DONE,
                            'track_id'              => Str::uuid()->toString(),
                            'done_at'               => $transaction->done_at,
                        ])
                            ->each(fn(Transaction $transaction) => $transaction->card->account()->update([
                                'balance' => $transaction->card->account->refresh()->balance->increase($transaction->amount)->getAmount()
                            ]))
                        )->each(fn(Transaction $transaction) => Fee::factory(1)
                            ->create([
                                'transaction_id' => $transaction->id,
                                'amount'         => $cardToCardFee
                            ])
                        )
                    )
                )
            );
    }
}
