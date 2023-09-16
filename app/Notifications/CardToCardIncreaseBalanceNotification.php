<?php

namespace App\Notifications;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\Channels\SmsChannel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CardToCardIncreaseBalanceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Transaction $deposit;

    private Transaction $withdraw;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly array $transactions)
    {
        $this->withdraw = $this->transactions['withdraw_transaction'];
        $this->deposit = $this->transactions['deposit_transaction'];
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return [SmsChannel::class];
    }

    public function toSms(): \Illuminate\Foundation\Application|array|string|Translator|Application|null
    {
        return __('notification.card_to_card.increase', [
            'amount' => $this->withdraw->amount->getAmount(),
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
        Log::critical('Card To Card Increase Balance Notification Failed', [
            'exception' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'transaction_id' => $this->withdraw->id,
        ]);
    }
}
