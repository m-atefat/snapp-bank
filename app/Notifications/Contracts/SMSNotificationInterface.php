<?php

namespace App\Notifications\Contracts;

interface SMSNotificationInterface
{
    public function toSms(): string;
}
