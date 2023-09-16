<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Notifications\CardToCardDecreaseBalanceNotification;
use App\Notifications\CardToCardIncreaseBalanceNotification;
use App\Services\SmsServices\Facade\SMS;

class SmsChannel
{
    /**
     * @param User $notifiable
     * @param CardToCardIncreaseBalanceNotification|CardToCardDecreaseBalanceNotification $notification
     */
    public function send(User $notifiable, CardToCardIncreaseBalanceNotification|CardToCardDecreaseBalanceNotification $notification): void
    {
        Sms::send($notifiable->routeNotificationForSms(), $notification->toSms());
    }
}
