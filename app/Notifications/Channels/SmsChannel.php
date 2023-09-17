<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Notifications\Contracts\SMSNotificationInterface;
use App\Services\SmsServices\Facade\SMS;
use Exception;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * @param User $notifiable
     * @param SMSNotificationInterface $notification
     */
    public function send(User $notifiable, SMSNotificationInterface $notification): void
    {
        try {
            Sms::send($notifiable->routeNotificationForSms(), $notification->toSms());
        } catch (Exception $exception) {
            Log::critical('Send SMS Notification Failed.', [
                'notification_type' => get_class($notification),
                'provider'          => config('notification.default_sms_provider')
            ]);

            report($exception);
        }
    }
}
