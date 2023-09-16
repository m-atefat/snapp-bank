<?php

namespace Tests\Feature;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Card;
use App\Models\Fee;
use App\Models\Transaction;
use App\Services\CardServices\CardServices;
use App\ValueObjects\Amount;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CardToCardTest extends TestCase
{
    public function testCardToCardSuccessfully(): void
    {
        $fee = config('fee.card_to_card');

        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstSourceBalance = Amount::forge(100000000)])->create())
            ->create();

        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstDestinationBalance = Amount::forge(100000)])->create())
            ->create();


        $response = $this->postJson(route('v1.cards.card-to-card'), [
            'source_card_number' => $sourceCard->number->toString(),
            'destination_card_number' => $destinationCard->number->toString(),
            'amount' => $amount = 10000,
        ]);

        $response->assertSuccessful();
        $response->assertJson(
            fn(AssertableJson $json) => $json
                ->has('status')
                ->has('tracking_code')
                ->has('error')
                ->etc()
        );

        $this->assertDatabaseHas(Transaction::class, [
            'card_id' => $sourceCard->id,
            'amount' => $amount + $fee,
            'type' => TransactionType::CARD_TO_CARD,
            'status' => TransactionStatus::DONE->value
        ]);

        $this->assertDatabaseHas(Transaction::class, [
            'card_id' => $destinationCard->id,
            'amount' => $amount,
            'type' => TransactionType::CARD_TO_CARD,
            'status' => TransactionStatus::DONE->value
        ]);

        $this->assertDatabaseCount(Fee::class, 1);

        $this->assertEquals($firstSourceBalance->decrease(Amount::forge($amount + $fee))->getAmount(), $sourceCard->account->balance->getAmount());
        $this->assertEquals($firstDestinationBalance->increase(Amount::forge($amount))->getAmount(), $destinationCard->account->balance->getAmount());
        Notification::assertCount(2);
    }

    public function testCardToCardFailedOnInvalidCardNumber(): void
    {
        $response = $this->postJson(route('v1.cards.card-to-card'), [
            'source_card_number' => '1234123412341234',
            'destination_card_number' => '4321432143214321',
            'amount' => 10000,
        ]);

        $response->assertUnprocessable();
        Notification::assertCount(0);
    }

    public function testCardToCardFailedOnSameCardNumber(): void
    {
        $sourceCard = Card::factory()->create();

        $response = $this->postJson(route('v1.cards.card-to-card'), [
            'source_card_number' => $sourceCard->number->toString(),
            'destination_card_number' => $sourceCard->number->toString(),
            'amount' => 10000,
        ]);

        $response->assertUnprocessable();
        Notification::assertCount(0);
    }

    public function testCardToCardFailedOnInvalidAmount(): void
    {
        Notification::fake();

        $fee = config('fee.card_to_card');
        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstSourceBalance = Amount::forge(100000000)])->create())
            ->create();

        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstDestinationBalance = Amount::forge(100000)])->create())
            ->create();


        $response = $this->postJson(route('v1.cards.card-to-card'), [
            'source_card_number' => $sourceCard->number->toString(),
            'destination_card_number' => $destinationCard->number->toString(),
            'amount' => 10,
        ]);


        $response->assertUnprocessable();

        $this->assertDatabaseCount(Transaction::class, 0);

        $this->assertEquals($firstSourceBalance->getAmount(), $sourceCard->account->balance->getAmount());
        $this->assertEquals($firstDestinationBalance->getAmount(), $destinationCard->account->balance->getAmount());


        Notification::assertCount(0);
    }

    public function testCardToCardFailedOnBalanceInsufficient(): void
    {
        Notification::fake();

        $sourceCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstSourceBalance = Amount::forge(50000)])->create())
            ->create();

        $destinationCard = Card::factory()
            ->for(Account::factory()->state(['balance' => $firstDestinationBalance = Amount::forge(100000)])->create())
            ->create();

        $response = $this->postJson(route('v1.cards.card-to-card'), [
            'source_card_number' => $sourceCard->number->toString(),
            'destination_card_number' => $destinationCard->number->toString(),
            'amount' => 5000000,
        ]);

        $response->assertSuccessful();
        $this->assertEquals(CardServices::INSUFFICIENT_BALANCE, $response->json('error'));

        $this->assertDatabaseCount(Transaction::class, 0);
        $this->assertDatabaseCount(Fee::class, 0);

        $this->assertEquals($firstSourceBalance->getAmount(), $sourceCard->account->balance->getAmount());
        $this->assertEquals($firstDestinationBalance->getAmount(), $destinationCard->account->balance->getAmount());
        Notification::assertCount(0);
    }
}
