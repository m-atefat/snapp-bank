<?php

namespace App\Services\SmsServices\Providers;

use App\Services\SmsServices\Contract\SmsProviderInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class Ghasedak implements SmsProviderInterface
{

    /**
     * @throws RequestException
     */
    public function send(string $mobile, string $message): void
    {
        Http::asForm()->baseUrl(config('notification.providers.ghasedak.base_url'))
            ->withHeader('apikey', config('notification.providers.ghasedak.api_key'))
            ->post(
                'sms/send/simple?'.http_build_query([
                    'receptor' => $mobile,
                    'message' => $message
                ])
            )
            ->throw();
    }
}
