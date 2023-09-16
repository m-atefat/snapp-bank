<?php

namespace App\ValueObjects;

use App\Exceptions\CardToCardAmountLimitationException;
use Throwable;

class CardToCardAmount
{
    public const MINIMUM_CARD_TO_CARD_AMOUNT = 1000;

    public const MAXIMUM_CARD_TO_CARD_AMOUNT = 50000000;

    private function __construct(private readonly Amount $amount)
    {
    }

    /**
     * @throws CardToCardAmountLimitationException
     * @throws Throwable
     */
    public static function forge(int|float $amount): self
    {
        $amount = Amount::forge($amount);
        static::validate($amount);
        return new self($amount);
    }

    /**
     * @throws Throwable
     * @throws CardToCardAmountLimitationException
     */
    private static function validate(Amount $amount): void
    {
        throw_if(
            $amount->getAmount() < self::MINIMUM_CARD_TO_CARD_AMOUNT ||
            $amount->getAmount() > self::MAXIMUM_CARD_TO_CARD_AMOUNT,
            CardToCardAmountLimitationException::class
        );
    }

    public static function isValid(Amount $amount): bool
    {
        try {
            static::validate($amount);
            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }

    public function getAmount(): int|float
    {
        return $this->amount->getAmount();
    }

    /**
     * @throws Throwable
     */
    public function increase(Amount $amount): self
    {
        return new self($this->amount->increase($amount));
    }

    /**
     * @throws Throwable
     */
    public function decrease(Amount $amount): self
    {
        return new self($this->amount->decrease($amount));
    }

    public function getAmountObject(): Amount
    {
        return $this->amount;
    }
}
