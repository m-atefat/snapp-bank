<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Contracts\SMSNotificationInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CardToCardDecreaseBalanceNotification extends Notification implements ShouldQueue, SMSNotificationInterface
{
    use Queueable;

    private Transaction $withdraw;

    private Transaction $deposit;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly array $transactions)
    {
        $this->withdraw = $this->transactions['withdraw_transaction'];
        $this->deposit = $this->transactions['deposit_transaction'];
    }


    public function via(User $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(): string
    {
        return __('notification.card_to_card.decrease', [
            'amount' => $this->withdraw->amount->getAmount(),
            'fee_amount' => $this->withdraw->fee->amount->getAmount(),
            'destination_name' => $this->deposit->card->account->user->name,
            'destination_card_number' => $this->deposit->card->number->mask(),
            'source_name' => $this->withdraw->card->account->user->name,
            'source_card_number' => $this->withdraw->card->number->mask(),
            'done_at' => $this->withdraw->done_at->format('Y-m-d H:i:s'),
            'track_id' => $this->withdraw->track_id,
        ]);
    }

    public function viaQueues(): array
    {
        return [
            SmsChannel::class => 'sms-queue'
        ];
    }

    public function failed(Exception $exception): void
    {
        Log::critical('Card To Card Decrease Balance Notification Failed', [
            'exception' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'transaction_id' => $this->withdraw->id,
        ]);
    }
}
