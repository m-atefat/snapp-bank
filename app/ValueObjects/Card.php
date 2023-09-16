<?php

namespace App\ValueObjects;

use App\Exceptions\InvalidCardNumberException;
use App\Exceptions\InvalidCardNumberLengthException;
use Illuminate\Support\Str;
use Throwable;

class Card
{
    private function __construct(private string $card)
    {
    }

    /**
     * @throws Throwable
     * @throws InvalidCardNumberLengthException
     * @throws InvalidCardNumberException
     */
    public static function fromString(string $cardNumber): self
    {
        $cardNumber = Str::remove('-', $cardNumber);
        static::validate($cardNumber);
        return new self($cardNumber);
    }

    public static function isValid(string $cardNumber): bool
    {
        try {
            static::validate($cardNumber);
            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    /**
     * @throws Throwable
     * @throws InvalidCardNumberLengthException
     * @throws InvalidCardNumberException
     */
    private static function validate(string $cardNumber): void
    {
        throw_if(!preg_match('/^\d{16}$/', $cardNumber), InvalidCardNumberLengthException::class);

        $sum = 0;
        for ($position = 1; $position <= 16; $position++) {
            $temp = $cardNumber[$position - 1];
            $temp = $position % 2 === 0 ? $temp : $temp * 2;
            $temp = $temp > 9 ? $temp - 9 : $temp;

            $sum += $temp;
        }

        throw_if($sum % 10 !== 0, InvalidCardNumberException::class);
    }

    public function toString(): string
    {
        return $this->card;
    }

    public function __toString(): string
    {
        return $this->card;
    }

    public function mask(): string
    {
        return Str::mask($this->toString(), '*', 4, 8);
    }
}
