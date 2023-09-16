<?php

namespace App\Services\SmsServices\Contract;

interface SmsProviderInterface
{
    public function send(string $mobile, string $message): void;
}
