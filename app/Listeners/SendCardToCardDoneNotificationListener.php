<?php

namespace App\Listeners;

use App\Events\CardToCardDoneEvent;
use App\Notifications\CardToCardDecreaseBalanceNotification;
use App\Notifications\CardToCardIncreaseBalanceNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCardToCardDoneNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CardToCardDoneEvent $event): void
    {
        $event->transactions['withdraw_transaction']
            ->card
            ->account
            ->user
            ->notify(new CardToCardDecreaseBalanceNotification($event->transactions));

        $event->transactions['deposit_transaction']
            ->card
            ->account
            ->user
            ->notify(new CardToCardIncreaseBalanceNotification($event->transactions));
    }
}
