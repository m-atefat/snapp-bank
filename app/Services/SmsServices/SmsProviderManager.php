<?php

namespace App\Services\SmsServices;

use App\Services\SmsServices\Contract\SmsProviderInterface;
use App\Services\SmsServices\Providers\Ghasedak;
use App\Services\SmsServices\Providers\Kavenegar;
use Illuminate\Support\Manager;

class SmsProviderManager extends Manager
{
    public function getDefaultDriver()
    {
        return config('notification.default_sms_provider');
    }

    public function createKavenegarDriver(): SmsProviderInterface
    {
        return app(Kavenegar::class);
    }

    public function createGhasedakDriver(): SmsProviderInterface
    {
        return app(Ghasedak::class);
    }
}
