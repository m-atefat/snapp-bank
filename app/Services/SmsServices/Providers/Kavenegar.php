<?php

namespace App\Services\SmsServices\Providers;

use App\Services\SmsServices\Contract\SmsProviderInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Kavenegar implements SmsProviderInterface
{
    /**
     * @throws RequestException
     */
    public function send(string $mobile, string $message): void
    {
        Http::baseUrl(
            config('notification.providers.kavenegar.base_url')."/".config(
                'notification.providers.kavenegar.api_key'
            )
        )
            ->get('sms/send.json', [
                'receptor' => $mobile,
                'message' => $message
            ])
            ->throw();
    }
}
