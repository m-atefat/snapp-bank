<?php

namespace App\Services\SmsServices\Facade;

use App\Services\SmsServices\SmsProviderManager;
use Closure;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SmsProviderManager driver(string $driver);
 * @method static SmsProviderManager extend(string $driver, Closure $callback);
 * @method static void send(string $number, string $message);
 */
class SMS extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmsProviderManager::class;
    }
}
